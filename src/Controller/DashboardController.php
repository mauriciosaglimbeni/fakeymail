<?php

namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Users;
use App\Entity\Messages;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**   
 @IsGranted("ROLE_USER")
*/
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]

    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $messages = $entityManager->getRepository(Messages::class)->findAll();
        $query = $entityManager->createQuery("select * from messages where recipient = ".this.User->email());
        $messages = $query->getResult();
        if($messages == null){
            return $this->render('dashboard/index.html.twig',['messages'=>"There are no messages yet!"]);
        }else {
            return $this->render('dashboard/index.html.twig',['messages'=>$messages]);
        }

       
    }
}
