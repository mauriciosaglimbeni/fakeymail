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
        
        $messages = $messageRepository ->findBy(
            ['recipient' => $user->getEmail()]
        );

            return $this->render('pages/index.html.twig',[
                'messages' => $messages
            ]);

    }
}
