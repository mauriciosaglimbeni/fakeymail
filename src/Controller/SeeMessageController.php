<?php

namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Users;
use App\Entity\Messages;
use App\Repository\MessagesRepository;
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

    public function index(MessagesRepository $messageRepository,EntityManagerInterface $entityManager ): Response
    {
            $id = $_GET['id'];
            $message = $messageRepository->findOneBy([
                'id' => $id
            ]);
            $message->setIsRead(1);
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->render('pages/seeMessage.html.twig',[
                'message' => $message ]);

    }
}
