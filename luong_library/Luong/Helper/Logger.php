<?php

class Luong_Helper_Logger {

    public function __construct() {
        Zend_Loader::loadClass("AuthModel");
    }

    public static function LogWriterUser($content = "", $logLevel = null) {

        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {

            if ($logLevel == null)
                $logLevel = Luong_Helper_Constant::LOG_INFO;

            $identity = $auth->getIdentity();

            $userName = str_replace(".", "", $identity->user_name);

            $date = new Zend_Date();

            if (!is_dir(APPLICATION_PATH . "/" . LogDefine::$_LOG_PATH)) {
                if (!mkdir($logPath = APPLICATION_PATH . "/" . LogDefine::$_LOG_PATH, 0777)) {
                    //echo "Cannot create directory ($logPath)";
                    exit;
                }
            }

            $logPath = APPLICATION_PATH . "/" . LogDefine::$_LOG_PATH . "/" . $date->toString('yyyyMM');

            if (!is_dir($logPath)) {
                if (!mkdir($logPath, 0777)) {
                    //echo "Cannot create directory ($logPath)";
                    exit;
                }
            }
            $filename = $logPath . "/" . $date->toString('yyyyMMdd') . ".log.txt";

            if (!is_file($filename)) {
                $ourFileHandle = fopen($filename, 'w'); // or die("can't open file ($filename)");
                fclose($ourFileHandle);
            }

            if ($logLevel == 0)
                $logLevel = Luong_Helper_Constant::LOG_INFO;

            $content = $logLevel . " - " . $date->toString('yyyyMMdd HH:mm:ss') . " " . Luong_Helper_Constant::USER_MEMBER . "-" . $identity->id . "-" . $userName . " " . $content;

            if (is_writable($filename)) {

                if (!$handle = fopen($filename, 'a')) {
                    //echo "Cannot open file ($filename)";
                    exit;
                }

                if (fwrite($handle, $content . " IP: " . $_SERVER["REMOTE_ADDR"] . "\n") === FALSE) {
                    //echo "Cannot write to file ($filename)";
                    exit;
                }

                //echo "Success, wrote ($content) to file ($filename)";

                fclose($handle);
            } else {
                //echo "The file $filename is not writable";
            }
        }
    }

    public static function LogWriterAdmin($content = "", $logLevel = null) {

        $auth = new AuthModel();
        if ($auth->hasIdentity()) {

            if ($logLevel == null)
                $logLevel = Luong_Helper_Constant::LOG_INFO;

            $identity = $auth->getIdentity();
            $userName = $identity["sunnet_email"];

            $date = new Zend_Date();

            if (!is_dir(APPLICATION_PATH . "/" . LogDefine::$_LOG_PATH)) {
                if (!mkdir($logPath = APPLICATION_PATH . "/" . LogDefine::$_LOG_PATH, 0777)) {
                    //echo "Cannot create directory ($logPath)";
                    exit;
                }
            }

            $logPath = APPLICATION_PATH . "/" . LogDefine::$_LOG_PATH . "/" . $date->toString('yyyyMM');

            if (!is_dir($logPath)) {
                if (!mkdir($logPath, 0777)) {
                    //echo "Cannot create directory ($logPath)";
                    exit;
                }
            }
            $filename = $logPath . "/" . $date->toString('yyyyMMdd') . ".log.txt";

            if (!is_file($filename)) {
                $ourFileHandle = fopen($filename, 'w'); // or die("can't open file ($filename)");
                fclose($ourFileHandle);
            }

            if ($logLevel == 0)
                $logLevel = Luong_Helper_Constant::LOG_INFO;

            $content = $logLevel . " - " . $date->toString('yyyyMMdd HH:mm:ss') . " " . Luong_Helper_Constant::USER_ADMIN . "-" . $identity["id"] . "-" . $userName . " " . $content;

            if (is_writable($filename)) {

                if (!$handle = fopen($filename, 'a')) {
                    //echo "Cannot open file ($filename)";
                    exit;
                }

                if (fwrite($handle, $content . " IP: " . $_SERVER["REMOTE_ADDR"] . "\n") === FALSE) {
                    //echo "Cannot write to file ($filename)";
                    exit;
                }

                //echo "Success, wrote ($content) to file ($filename)";

                fclose($handle);
            } else {
                //echo "The file $filename is not writable";
            }
        }
    }

    public static function writeMailLog($mailData) {

        try {
            Zend_Loader::loadClass("admin_MailModel");
            $mailModel = new Admin_MailModel();
            if (isset($mailData["cc"]))
                $cc = $mailData["cc"];
            else
                $cc = 1;
            $data = array('to_email' => $mailData["to_email"], 'mail_title' => $mailData["mail_title"], 'mail_content' => $mailData["mail_content"], 'type' => $mailData["type"], 'level' => $mailData["level"], 'cc' => $cc);
            $i = $mailModel->insert($data);
        } catch (Exception $ex) {
            //Zend_Debug::dump($ex);
            echo $ex->getMessage();
        }
    }

    public static function addMember($member = "") {
        $logPath = APPLICATION_PATH . "/maillog";

        if (!is_dir($logPath)) {
            if (!mkdir($logPath, 0777)) {
                //echo "Cannot create directory ($logPath)";
                exit;
            }
        }

        $memberfile = $logPath . "/" . "member.txt";
        if (!is_file($memberfile)) {
            $ourFileHandle = fopen($memberfile, 'w'); // or die("can't open file ($filename)");


            Zend_Loader::loadClass("UserModel");
            $uModel = new UserModel();
            $users = $uModel->getList1(array('status' => 1));
            $strUser = implode(",", $users);


            if (fwrite($ourFileHandle, $strUser) === FALSE) {
                //echo "Cannot write to file ($filename)";
                exit;
            }

            fclose($ourFileHandle);
        }

        if (is_writable($memberfile) && $member != "") {

            if (!$handle = fopen($memberfile, 'a')) {
                //echo "Cannot open file ($filename)";
                exit;
            }

            if (fwrite($handle, "," . $member) === FALSE) {
                //echo "Cannot write to file ($filename)";
                exit;
            }

            //echo "Success, wrote ($content) to file ($filename)";

            fclose($handle);
        } else {
            //echo "The file $filename is not writable";
        }
    }

}

class LogDefine {

    public static $_LOG_PATH = LOG_DIR;

}

?>