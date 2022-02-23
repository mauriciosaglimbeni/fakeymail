<?php

namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Entity\Messages;
use App\Repository\UserRepository;
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

    public function outbox(MessagesRepository $messageRepository ,UserRepository $userRepository): Response
    {
        /** @var \App\Entity\User */
        $user = $this->getUser();
        $messages = $messageRepository ->createQueryBuilder('m')
        ->andWhere("m.sender = :val")
        ->setParameter('val', $user->getEmail())
        ->orderBy('m.created_at', 'DESC')
        ->getQuery()
        ->getResult();
        $recipient = array();
        for($i = 0;$i < count($messages);$i++){

            $recipient[$i] = $userRepository ->findOneBy([
                'email' => $messages[$i]->getRecipient()
            ]);
        }

            return $this->render('pages/outbox.html.twig',[
                'messages' => $messages, 'recipient' => $recipient
            ]);

    }
}