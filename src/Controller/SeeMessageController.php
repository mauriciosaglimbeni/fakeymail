<?php

namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Users;
use App\Entity\Messages;
use App\Repository\MessagesRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**   
 @IsGranted("ROLE_USER")
*/
class SeeMessageController extends AbstractController
{
    #[Route('/seeMessage', name: 'seeMessage')]

    public function index(UserRepository $userRepository, MessagesRepository $messageRepository,EntityManagerInterface $entityManager ): Response
    {
             /** @var \App\Entity\User */
             $user = $this->getUser();
            $id = $_GET['id'];
            $message = $messageRepository->findOneBy([
                'id' => $id
            ]);
            if($user->getEmail() == $message->getRecipient()){
                $message->setIsRead(1);
                $entityManager->persist($message);
                $entityManager->flush(); 
                $otherUser = $userRepository -> findOneBy([
                'email' => $message->getSender()
                ]);
            }else{
                $otherUser = $userRepository -> findOneBy([
                    'email' => $message->getRecipient()
                    ]);
            }

            return $this->render('pages/seeMessage.html.twig',[
                'message' => $message , 'otherUser' => $otherUser]);

    }
}
