<?php
// Controller dependencies
namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\User;
use App\Entity\Messages;
use App\Repository\MessagesRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**   
 @IsGranted("ROLE_USER")
*/
class DashboardController extends AbstractController
{
    // Routing, establishing the url to access this controller
    #[Route('/dashboard', name: 'dashboard')]

    public function index(MessagesRepository $messageRepository,UserRepository $userRepository ): Response
    {
        // we get the current user  and the current user email
        /** @var \App\Entity\User */
        $user = $this->getUser();
        $email = $user->getEmail();
        // with the user´s email we find messages and order them by date.
        $messages = $messageRepository ->createQueryBuilder('m')
            ->andWhere("m.recipient = :val")
            ->setParameter('val', $email)
            ->orderBy('m.created_at', 'DESC')
            ->getQuery()
            ->getResult();
        // we create an array for senders, we will use this array to save sender user information in otder to get each individual user data and show it i the template
        $sender = array();
        // we create a sender for each message this user has received, we then save each sender data into the array.
        for($i = 0;$i < count($messages);$i++){

            $sender[$i] = $userRepository ->findOneBy([
                'email' => $messages[$i]->getSender()
            ]);
        }
            // we return the template of index with messages and senders.
            return $this->render('pages/index.html.twig',[
                'messages' => $messages, 'sender' => $sender
            ]);

    }
}
