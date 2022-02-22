<?php

namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Users;
use App\Entity\Messages;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
/**   
 @IsGranted("ROLE_USER")
*/
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]

    public function index(UserRepository $userRepository ): Response
    {
            $email = $_GET['email'];
            $user = $userRepository->findOneBy([
                'email' => $email
            ]);
            return $this->render('pages/profile.html.twig',[
                'user' => $user
            ]);

    }
}
