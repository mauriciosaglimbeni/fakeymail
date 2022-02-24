<?php

namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**   
 @IsGranted("ROLE_USER")
*/
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]

    public function index(UserRepository $userRepository ): Response
    {
            /** @var \App\Entity\User */
            $currentUser = $this->getUser();

            $email = $_GET['email'];
            $user = $userRepository->findOneBy([
                'email' => $email
            ]);
            if($email == $currentUser->getEmail()){
                
            }
            return $this->render('pages/profile.html.twig',[
                'user' => $user
            ]);

    }
}
