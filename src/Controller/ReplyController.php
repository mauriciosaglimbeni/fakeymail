<?php

namespace App\Controller;
// controller dependencies
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Users;
use App\Entity\Messages;
use App\Form\ReplyFormType;
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
class ReplyController extends AbstractController
{
    #[Route('/reply', name: 'reply')]
    public function newMsg(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager, UserRepository $userRepository, MessagesRepository $messageRepository, ManagerRegistry $doctrine): Response
    {
        // getting the current user data
        /** @var \App\Entity\User */
        $user = $this->getUser();
        // GETTING MSG ID and finding it in database
        $id = $_GET['id'];
        $prevMsg = $messageRepository->findOneBy([
            'id' => $id
        ]);

        // Setting up the form for a new message
        $message = new Messages();
        $form = $this->createForm(ReplyFormType::class, $message);
        $form->handleRequest($request);
        // checking if form was sent
        if ($form->isSubmitted() && $form->isValid()) {
            // setting the subject in the form to RE: subject and uploading it
            $subject = "RE:" . $prevMsg->getSubject();
            $message->setSubject($subject);
            // sender settings
            $sender = $user->getEmail();
            $message->setSender($sender);
            // isRead settings
            $message->setIsRead(0);
            // date settings
            $today = new \DateTimeImmutable('now');
            $message->setCreatedAt($today);
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
            // puts the recipient as the previous sender and creates a message
            $recipient = $prevMsg->getSender();
            $message->setRecipient("$recipient");
            $entityManager->persist($message);
            $entityManager->flush();

            // redirects to the outbox route
            return $this->redirectToRoute('outbox');
        }
        // returns the template with the form (see ReplyForm.php) and the previous message information
        return $this->render('pages/reply.html.twig', [
            'replyForm' => $form->createView(),
            'prevMsg' => $prevMsg
        ]);
    }
}
