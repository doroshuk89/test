<?php


namespace Controller;


class ProfileuserController extends DisplayController
{
        public function profile () {
            //Запускам класс постредник для проверки авторизован ли пользователь
            $this->middle->isUserLogin($this->lang_prefix);
                return $this->display();
        }

        protected function display()
        {
            $this->mainbar = $this->mainBar();
            parent::display(); // TODO: Change the autogenerated stub
        }

        protected function mainBar () {
            $data = $this->view->render('profile.php');
        return $data;
    }
}