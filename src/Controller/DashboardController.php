<?php

namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Users;
use App\Entity\Messages;
use App\Repository\MessagesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
/**   
 @IsGranted("ROLE_USER")
*/
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]

    public function index(MessagesRepository $messageRepository ): Response
    {
        /** @var \App\Entity\User */
        $user = $this->getUser();
        $email = $user->getEmail();
        $messages = $messageRepository ->createQueryBuilder('m')
            ->andWhere("m.recipient = :val")
            ->setParameter('val', $email)
            ->orderBy('m.created_at', 'DESC')
            ->getQuery()
            ->getResult();
        

            return $this->render('pages/index.html.twig',[
                'messages' => $messages
            ]);

    }
}
