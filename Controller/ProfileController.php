<?php
/**
 * Created by PhpStorm.
 * User: sow
 * Date: 05/05/17
 * Time: 20:18
 */

namespace Controller;
use Model\FileManager;
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;
use Model\UserManager;


class ProfileController extends BaseController
{
    public function profileAction()
    {

        if (!empty($_SESSION['user_id'])) {
            $userManager = UserManager::getInstance();
            $fileManager = FileManager::getInstance();
            $user = $userManager->getUserById((int)$_SESSION['user_id']);
            if(isset($_POST['submitAddFile'])){
                $res = $fileManager->fileCheckAdd($_POST);
                if($res['isFormGood']){
                    $fileManager->addFile($res['data']);
                }else{
                    var_dump($res['errors']);
                }
            }
            echo $this->renderView('profile.html.twig',
                                    ['user' => $user,]
                );
        } else {
            $this->redirect('home');
        }


    }


    public function adminAction()
    {
        $offers = array();
        if (!empty($_SESSION['user_id'] == '1')) {
            $manager = UserManager::getInstance();
            $user_id = $_SESSION['user_id'];
            $user = $manager->getUserById($user_id);
            $errors = array();
            $manager->getAllDeals();
            $surveys = $manager->getSurvey();
            $allVotes = $manager->allVotes();  //for average
            $pageActuel = $_GET['action'];
            $newsRegister = '';

            if (isset($_POST['submitNewsletter'])) {
                $res = $manager->newsletterCheck($_POST['newsletter']);
                if($res['isFormGood']){
                    $manager->addMail($_POST);
                    $res = $manager->newslettersSend($res['data']);
                    $email = $res['email'];
                    $object = $res['object'];
                    $content = $res['content'];
                    $this->sendMail($email,$object,$content,'...');
                    $newsRegister = "Merci de vous etre abonnés a la NewsLetter, nous vous avons envoyé un email afin de vérifier votre adresse !";

                }
            }

            if (isset($_POST['submitCatalog'])) {
                $res = $manager->checkCatalog($_POST);
                if ($res['isFormGood']) {
                    $manager->addCatalog($res['data']);
                } else {
                    $errors = $res['errors'];
                }
            }

            if (isset($_POST['submitAddSurvey'])) {
                $res = $manager->checkSurvey($_POST);
                if ($res['isFormGood']) {
                    $manager->addSurveyTmp($res['data']);
                    $data = $manager->countSurveyTmp();
                    foreach ($data as $value){
                        if((int)$value['COUNT(*)'] == 3){
                            $surveysTmp = $manager->getSurveyTmp();
                            foreach ($surveysTmp as $value){
                                $manager->addSurvey($value);
                            }
                            $manager->removeSurveyTmp();
                        }
                    }
                } else {
                    $errors = $res['errors'];
                }
            }

            $res_tmp = $manager->surveyNumber();
            if(is_array($res_tmp) && !empty($res_tmp)){
                $offers[] = $res_tmp[0];
                if(isset($res_tmp[1])){
                    $offers[] = $res_tmp[1];
                }
                if(isset($res_tmp[2])){
                    $offers[] = $res_tmp[2];
                }
            }


            if (isset($_POST['submitAccount'])) {
                if ($manager->checkRemoveAccount($_POST)) {
                    $manager->deleteAccount($_POST);
                }
            }

            if (isset($_POST['deletteOffers'])) {
                if ($manager->checkRemoveOffers($_POST['offers'])) {
                    $manager->removeOffer($_POST['offers']);
                }
            }

            $dealToUpdate = array();
            if (isset($_POST['submitChoiceOffer'])) {
                $dealToUpdate = $manager->getDealByTitle($_POST['listOffer']);
            }
            if (isset($_POST['submitUpdateOffer'])) {
                $res = $manager->checkUpdateOffer($_POST);
                if($res['isFormGood']){
                    $manager->updateOffer($res['data']);
                }
            }
            if (isset($_POST['submitRemoveOffer'])) {
                $manager->removeOffer($_POST['partner']);
            }

            if (isset($_POST['submitBottles'])) {
                if ($manager->checkDump($_POST)) {
                    $manager->addBarcode($_POST);
                }
            }


            if(isset($_POST['submitSendGeneralMail'])){
                $allMails = $manager->getAllEmails();
                if($manager->checkSendNews($_POST)){
                    foreach ($allMails as $value){
                        $email = $value['email'];
                        $object = $_POST['titreNewsletter'];
                        $content = "<html>
                <head>
                <title>Vous avez réservé sur notre site ...</title>
                </head>
                <body>
                <p>" . $_POST['newsletterContent'] . "</p>
                <p>Cordialement</p>
                <p>La fondation Tritus</p>
                <p>Contact: tritusfundation@gmail.com</p>
                </body>
                </html>";

                        $this->sendMail($email,$object,$content,'...');
                    }
                }
            }

            $deals = $manager->getAllDeals();
            echo $this->renderView('admin.html.twig',
                [
                    'user' => $user,
                    'errors' => $errors,
                    'deals' => $deals,
                    'dealToUpdate' => $dealToUpdate,
                    'surveys' => $surveys,
                    'allVotes' => $allVotes,
                    'offers' => $offers,
                    'pageActuel' => $pageActuel,
                    'newsRegister' => $newsRegister,
                ]);
        } else {
            $this->redirect('home');
        }
    }

}