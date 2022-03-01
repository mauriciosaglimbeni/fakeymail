<?php

namespace App\Controller;
// controller dependencies
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
/**   
 @IsGranted("ROLE_USER")
*/
class ProfileController extends AbstractController
{
    // routing
    #[Route('/profile', name: 'profile')]

    public function index(UserRepository $userRepository ,SluggerInterface $slugger,EntityManagerInterface $entityManager): Response
    {
        // we get the current user data
            /** @var \App\Entity\User */
            $currentUser = $this->getUser();
        // we get the value received on the GET to see which user´s profile we are seeing and we find that information and save it on user
            $email = $_GET['email'];
            $user = $userRepository->findOneBy([
                'email' => $email
            ]);
           
            // we check if the logged user is the profile user, if it is, we treat the change profile form with vanilla php, else, we only show information
            if($email == $currentUser->getEmail()){
                
                // Form handling in vanilla PHP
                if(isset($_POST['submit'])){
                    if(isset($_POST['name'])){
                        if(empty($_POST["name"])){
                            echo("Name is required!");
                        }else{
                            $currentUser->setName($_POST['name']);
                        }
                    }
                    if(isset($_POST['status'])){
                      $currentUser->setStatus($_POST['status']);   
                    }
                   if(isset($_POST['age']) && $_POST['age'] = ''){
                        $currentUser->setAge($_POST['age']);
                   }
                    if(isset($_FILES['pfp'])){
                        $filename = $_FILES['pfp']['name'];
                        $file_tmp =$_FILES['pfp']['tmp_name'];
                        try {
                            move_uploaded_file($file_tmp,$this->getParameter('uploads').$filename);
                        } catch (FileException $e) {
                            // ... handle exception if something happens during file upload
                            echo("Could not upload file");
                        }
                        // updates the 'pfpname' property to store the image file name
                        $user->setPfp($filename);

                    } 
                    // Updating the database
                        $entityManager->persist($currentUser);
                        $entityManager->flush(); 
                }
            }
            // return profile render with the profile user we got from the get
            return $this->render('pages/profile.html.twig',[
                'user' => $user
            ]);

    }
}
