<?php

namespace App\Controller;
// controller dependencies
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
    // routing
    #[Route('/seeMessage', name: 'seeMessage')]

    public function index(UserRepository $userRepository, MessagesRepository $messageRepository,EntityManagerInterface $entityManager ): Response
    {
        // getting current user data
             /** @var \App\Entity\User */
             $user = $this->getUser();
        // getting message from GET by Id and getting its information
            $id = $_GET['id'];
            $message = $messageRepository->findOneBy([
                'id' => $id
            ]);
        // When the logged user is the sender, we get the recipient´s info, and we do the opposite if the logged user is the receiver.
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
            // return the seeMessage template with the message and whoever the otherUser is (sender or recipient).
            return $this->render('pages/seeMessage.html.twig',[
                'message' => $message , 'otherUser' => $otherUser]);

    }
}
