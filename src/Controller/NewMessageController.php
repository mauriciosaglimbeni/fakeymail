<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Users;
use App\Entity\Messages;
use App\Form\MessageFormType;
use App\Repository\MessagesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Length;

/**   
 @IsGranted("ROLE_USER")
 */
class NewMessageController extends AbstractController
{
    #[Route('/newMessage', name: 'newMessage')]
    public function newMsg(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager, UserRepository $userRepository, MessagesRepository $messageRepository, ManagerRegistry $doctrine): Response
    {
        // getting the current user data
        /** @var \App\Entity\User */
        $user = $this->getUser();
        // getting all users
        $users = $userRepository->findAll();
        // MAKING A LOOP FOR EACH USER IN THE DB 
        for ($i = 0; $i < count($users); $i++) {
            // Setting up the form for a new message
            $message = new Messages();
            $form = $this->createForm(MessageFormType::class, $message);
            $form->handleRequest($request);
            // Checking the form and making sure it was succesful
            if ($form->isSubmitted() && $form->isValid()) {
                // breaking out of the loop when it reaches the selected recipient count so it doesnt send more messages
                if ($i == count($_POST['recipients'])) {
                    break;
                }
                // image settings
                $img = $form->get('image')->getData();
                if ($img) {

                    $originalFilename = pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $img->guessExtension();
                    // try {
                    $img->move(
                        $this->getParameter('uploads'),
                        $newFilename
                    );
                    // } catch (FileException $e) {
                    //     // ... handle exception if something happens during file upload
                    //     echo ("Could not upload image");
                    // }
                    $message->setImage($newFilename);
                }
                // file settings
                $fl = $form->get('file')->getData();
                if ($fl) {
                    $originalFilename = pathinfo($fl->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $fl->guessExtension();
                    try {
                        $fl->move(
                            $this->getParameter('uploads'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                        echo ("Could not upload file");
                    }
                    $message->setFile($newFilename);
                }

                // subject empty settings
                $subject = $form->get('subject')->getData();
                if ($subject) {
                    $message->setSubject($subject);
                } else {
                    $message->setSubject("NO SUBJECT");
                }

                // sender settings
                $sender = $user->getEmail();
                $message->setSender($sender);

                // isRead settings
                $message->setIsRead(0);

                // date settings
                $today = new \DateTimeImmutable('now');
                $message->setCreatedAt($today);

                // saves the recipient and creates a message

                $recipient = $_POST['recipients'][$i];
                $message->setRecipient("$recipient");
                $entityManager->persist($message);
                $entityManager->flush();

                // redirects to the outbox route
                return $this->redirectToRoute('outbox');
            }
        }

        return $this->render('pages/newMessage.html.twig', [
            'newMessageForm' => $form->createView(),
            'users' => $users
        ]);
    }
}
