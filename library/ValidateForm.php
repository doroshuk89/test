<?php


namespace Library;


class ValidateForm
{
    //Массив ошибок
    public $err = array();
    public $model;
    protected $lang;
    //Массив запрещенных расширений файлов
    protected $deny = array(
        'phtml', 'php', 'php3', 'php4', 'php5', 'php6', 'php7', 'phps', 'cgi', 'pl', 'asp',
        'aspx', 'shtml', 'shtm', 'htaccess', 'htpasswd', 'ini', 'log', 'sh', 'js', 'html',
        'htm', 'css', 'sql', 'spl', 'scgi', 'fcgi', 'txt'
    );
    protected $limitBytes = 1024*1024*5 ;

    public function __construct($container)
    {
        $this->model=$container['model'];
    }

    //Функция удаления тегов и пробелов в начале и конце
    public function clearStr($str) {
        return strip_tags(trim($str));
    }

    //Функция добавления запрещенного расширения файла
    public function setDenyFormat ($item) {
        if (!empty($item)) {
            $this->deny [] = $item;
        }

    }

    public function validateRegForm (array $form, array $files, array $lang = null) {
        //Массив с текущем языком
        if ($lang) {
            $this->lang = $lang;
        }
                //Проверка поля формы имя (name)
                if (isset($form['name'])) {
                    $this->ValidateName($this->clearStr($form['name']), $key = 'name');
                }
                //Проверка формы логина
                if (isset($form['login'])){
                    $this->validateLogin($this->clearStr($form['login']),$key = 'login');
                }
                //Проверка поля email
                if (isset($form['email'])) {
                    $this->ValidateEmail($this->clearStr($form['email']), $key = 'email');
                }
                //Проверка поля password
                if (isset($form['password']) ) {
                    $this->ValidatePassword($this->clearStr($form['password']),$key = 'password');
                }
                //Проверка поля подтверждения password
                if (isset($form['password_confirm'])) {
                    $this->ValidatePassword($this->clearStr($form['password_confirm']), $key = 'password_confirm');
                }

                //Проверка файла
                if (isset($files['file']) && !empty($files['file']['name']) && empty($files['file']['error'])) {
                    $this->ValidFile($files['file'], $key = 'upload_form');
                }

        //Возвращаем массив с возможными ошибками валидации
        return $this->err;
}
    protected function addErrorArray ($nameInput,$message) {
        $this->err [$nameInput]['message'] = $message;
        $this->err [$nameInput]['field'] = $nameInput;
    }

    //Функция валидации поля Имя (Name)
    protected function ValidateName ($name, $key) {
            //Проверка на пустоту
            if (strlen($name) == 0) {
                    $this->addErrorArray($key, $this->lang['valid_back_name_empty']);
                return true;
            }
            //Проверка длины логина
            if (strlen($name) < 3) {
                    $this->addErrorArray($key, $this->lang['valid_back_name_length']);
                return true;
            }
            //Запрет специальных символов в имени пользователя
            if (preg_match("/[\~`!@#$%\^&*()+=\-\[\]\\';,{}|\\\":<>\?]+/", $name)) {
                    $this->addErrorArray($key, $this->lang['valid_back_name_deny']);
                return true;
            }
        }
    //Функция валидации Логина
    public function validateLogin($login, $key)
    {
        //Проверка на пустоту
        if (strlen($login) == 0) {
                $this->addErrorArray($key, $this->lang['valid_back_login_empty']);
            return true;
        }
        //Проверка длины логина
        if (strlen($login) < 3) {
                $this->addErrorArray($key, $this->lang['valid_back_login_length']);
            return true;
        }
        //Проверка разрешенных симоволов
        if (!preg_match("/^[a-zA-Z0-9_]+$/", $login)) {
                $this->addErrorArray($key, $this->lang['valid_back_login_pattern']);
            return true;
        }
        //Проверка наличия указанного логина в БД
        if ($this->checkLogin($login)) {
                $this->addErrorArray($key, $this->lang['valid_back_login_not_free']);
            return true;
        }
    }

    //Функция проверки поля email
    protected function ValidateEmail ($email, $key) {
        //Проверка на пустоту
        if (strlen($email) == 0) {
                $this->addErrorArray($key, $this->lang['valid_back_email_empty']);
            return true;
        }
        //Соответствие шаблону
        if (!preg_match("/^[a-z0-9_-]+@[a-z-]+\.([a-z]{1,6}\.)?[a-z]{2,6}$/i", $email)) {
                $this->addErrorArray($key, $this->lang['valid_back_email_pattern']);
            return true;
        }

        //Проверка на наличие в БД email
        if ($this->checkMail($email)){
                $this->addErrorArray($key, $this->lang['valid_back_email_not_free']);
            return true;
        }
    }


    //Функция проверки пароля
    public function ValidatePassword ($pass, $key) {
        //Поверка на пустоту
        if (strlen($pass) == 0) {
                $this->addErrorArray($key, $this->lang['valid_back_pass_empty']);
            return true;
        }
        //Проверка длины пароля
        if (strlen($pass) < 6 ) {
                $this->addErrorArray($key, $this->lang['valid_back_pass_length']);
            return true;
        }
        //Запрет русских букв
        if (preg_match("~[а-яА-Я]+~", $pass)) {
                $this->addErrorArray($key, $this->lang['valid_back_pass_not_rus']);
            return true;
        }
    }

    //Функция валидации файла изображения передано через форму
    protected function ValidFile($file, $key) {
            //Путь к файлу
            $filepath  = $file['tmp_name'];
            //Ниличие ошибок
            $errorCode = $file['error'];
        if ($errorCode !== UPLOAD_ERR_OK || !is_uploaded_file($filepath)) {
              $this->addErrorArray($key, $this->lang['valid_back_file_error']);
            return true;
        }

        //Проверка расширения загруженного файла
        foreach ($this->deny as $item) {
            if (preg_match("/$item\$/i", $file['name'])) {
                    $this->addErrorArray($key, $this->lang['valid_back_file_deny_extension']);
                return true;
            }
        }

        //Проверка MIME тип файла изображения использую расширение FileInfo
        //Создадим ресурс FileInfo
        $fi = finfo_open(FILEINFO_MIME_TYPE);
        // Получим MIME-тип
        $mime = (string) finfo_file($fi, $filepath);
        // Проверим ключевое слово image (image/jpeg, image/png и т. д.)
                if (strpos($mime, 'image') === false) {
                        $this->addErrorArray($key, $this->lang['valid_back_file_deny_type']);
                    return true;
                }


                //Функция проверки размера изображения
        if (filesize($filepath) > $this->limitBytes) {
                    $this->addErrorArray($key, $this->lang['valid_back_file_size']);
                return true;
            }
        }

    //Функция проверки наличия логина в БД
    public function checkLogin ($login) {
        if ($login) {
            $result = $this->model->checkLogin($login);
            if (isset($result[0]['id']) && !empty($result[0]['id'])) {
                return TRUE;
            }else
                return FALSE;
        }
    }

    //Функция проверки наличия email в БД
    public function checkMail ($email) {
        $result = $this->model->checkMail($email);
        if (isset($result[0]['id']) && !empty($result[0]['id'])) {
            return TRUE;
        }else
            return FALSE;
    }

}