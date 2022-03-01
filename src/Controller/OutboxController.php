<?php

namespace App\Controller;
// controller dependencies
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
    // routing
    #[Route('/outbox', name: 'outbox')]

    public function outbox(MessagesRepository $messageRepository ,UserRepository $userRepository): Response
    {
        // we get the current user data and messages.
        /** @var \App\Entity\User */
        $user = $this->getUser();
        // we order the data by date where the messages were sent by the current user.
        $messages = $messageRepository ->createQueryBuilder('m')
        ->andWhere("m.sender = :val")
        ->setParameter('val', $user->getEmail())
        ->orderBy('m.created_at', 'DESC')
        ->getQuery()
        ->getResult();
        // we create an array for all recipients in order to get their individual information (profile picture)
        $recipient = array();
        for($i = 0;$i < count($messages);$i++){

            $recipient[$i] = $userRepository ->findOneBy([
                'email' => $messages[$i]->getRecipient()
            ]);
        }
        // we return the template of outbox with messages and recipients.
            return $this->render('pages/outbox.html.twig',[
                'messages' => $messages, 'recipient' => $recipient
            ]);

    }
}