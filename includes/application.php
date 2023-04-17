<?php

namespace Advanced_Product;

defined('ADVANCED_PRODUCT') or exit();

if (!class_exists('Advanced_Product\Application')) {

    class Application
    {

        protected $_message_queue = array();
        protected static $instance;

        public static function get_instance()
        {
            if (self::$instance && self::$instance instanceof Application) {
                return self::$instance;
            }

            self::$instance = new Application();
            return self::$instance;
        }

        public function enqueue_message($msg, $type = 'message', $options = array())
        {

            // Don't add empty messages.
            if (trim($msg) === '') {
                return;
            }

            // For empty queue, if messages exists in the session, enqueue them first.
            $messages = $this->get_message_queue();

            $message = array('message' => $msg, 'type' => strtolower($type), 'options' => $options);

            if (!in_array($message, $this->_message_queue)) {
                // Enqueue the message.
                $this->_message_queue[] = $message;
            }
            session_start();
            $_SESSION[ADVANCED_PRODUCT . '.application.queue'] = $this->_message_queue;
        }

        public function get_message_queue($clear = false)
        {
            // For empty queue, if messages exists in the session, enqueue them.
            if (!$this->_message_queue) {
                if( empty(session_id()) && !headers_sent()){
                    session_start();
                }
                $sessionQueue = (isset($_SESSION[ADVANCED_PRODUCT . '.application.queue'])
                    && $_SESSION[ADVANCED_PRODUCT . '.application.queue']) ?
                    $_SESSION[ADVANCED_PRODUCT . '.application.queue'] : array();

                if ($sessionQueue) {
                    $this->_message_queue = array_unique($sessionQueue);
                    $_SESSION[ADVANCED_PRODUCT . '.application.queue'] = array();
                }
            }

            $messageQueue = $this->_message_queue;

            if ($clear) {
                $this->_message_queue = array();
            }

            return $messageQueue;
        }
    }
}