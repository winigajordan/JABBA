<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Repository\BlogRepository;
use App\Repository\CategorieBlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/blog')]
class BlogController extends AbstractController
{
    private CategorieBlogRepository $catRipo;
    private BlogRepository $blogRepository;
    private EntityManagerInterface $em;

    /**
     * @param CategorieBlogRepository $catRipo
     * @param BlogRepository $blogRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(CategorieBlogRepository $catRipo, BlogRepository $blogRepository, EntityManagerInterface $em)
    {
        $this->catRipo = $catRipo;
        $this->blogRepository = $blogRepository;
        $this->em = $em;
    }

    #[Route('/{filter}', name: 'app_blog', defaults: ['filter'=>'all'])]
    public function index($filter, SessionInterface $session): Response
    {

        $output = $this->outputData($filter);

        $session->set('filter', $filter);
        return $this->render('blog/index.html.twig', [
            'articles'=>$output,
            'categories'=>$this->catRipo->findAll()
        ]);
    }

    public function outputData($filter){
        if ($filter =='all')
        {
            return $this->blogRepository->findBy(['etat'=>'PUBLIC']);
        } elseif ($filter=='mine'){
            return $this->blogRepository->findBy(['auteur'=>$this->getUser()]);
        } else {
            $cat = $this->catRipo->findOneBy(['slug'=>$filter]);
            return $this->blogRepository->findBy(['categorie'=>$cat]);
        }
    }

    #[Route('/detail/{slug}', name: 'app_blog_details')]
    public function details($slug, SessionInterface $session){
        $filter = $session->get('filter', 'all');
        $output = $this->outputData($filter);
        return $this->render('blog/index.html.twig', [
            'articles'=>$output,
            'categories'=>$this->catRipo->findAll(),
            'selected' => $this->blogRepository->findOneBy(['slug'=>$slug])
        ]);
    }

    #[Route('/commentaire/add', name: 'app_blog_commenter')]
    public function commenter(Request $request){
        if(!$this->getUser()){
            return $this->redirectToRoute('login');
        }
        $comment = new Commentaire();
        $comment -> setContenu($request->request->get('commentaire'));
        $comment -> setDate(new \DateTime());
        $comment -> setAuteur($this->getUser());
        $comment -> setEtat('VISIBLE');
        $comment -> setBlog($this->blogRepository->findOneBy(['slug'=>$request->request->get('slug')]));
        $this -> em -> persist($comment);
        $this -> em -> flush();
        return $this->redirectToRoute('app_blog_details', ['slug'=>$request->request->get('slug')]);
    }

    #[Route('/add/new', name: 'app_blog_add')]
    public function add(){
       return $this->render(
           'blog/add.html.twig', [
               'categories'=>$this->catRipo->findBy(['etat'=>'VALIDE']),
           ]
       );
    }
}
