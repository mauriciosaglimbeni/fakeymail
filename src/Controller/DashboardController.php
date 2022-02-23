<?php

namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\User;
use App\Entity\Messages;
use App\Repository\MessagesRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Length;

/**   
 @IsGranted("ROLE_USER")
*/
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]

    public function index(MessagesRepository $messageRepository,UserRepository $userRepository ): Response
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
            
        $sender = array();
        for($i = 0;$i < count($messages);$i++){

            $sender[$i] = $userRepository ->findOneBy([
                'email' => $messages[$i]->getSender()
            ]);
        }

            return $this->render('pages/index.html.twig',[
                'messages' => $messages, 'sender' => $sender
            ]);

    }
}
