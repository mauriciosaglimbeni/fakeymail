<?php

namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Users;
use App\Entity\Messages;
use App\Repository\MessagesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**   
 @IsGranted("ROLE_USER")
*/
class OutboxController extends AbstractController
{
    #[Route('/outbox', name: 'outbox')]

    public function outbox(MessagesRepository $messageRepository ): Response
    {
        /** @var \App\Entity\User */
        $user = $this->getUser();
        $messages = $messageRepository ->createQueryBuilder('m')
        ->andWhere("m.sender = :val")
        ->setParameter('val', $user->getEmail())
        ->orderBy('m.created_at', 'DESC')
        ->getQuery()
        ->getResult();

            return $this->render('pages/outbox.html.twig',[
                'messages' => $messages
            ]);

    }
}