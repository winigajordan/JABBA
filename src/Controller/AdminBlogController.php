<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Repository\BlogRepository;
use App\Repository\CategorieBlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/blog')]
class AdminBlogController extends AbstractController
{
    private EntityManagerInterface $em;
    private BlogRepository $blogRipo;
    private CategorieBlogRepository $catRipo;

    /**
     * @param EntityManagerInterface $em
     * @param BlogRepository $blogRipo
     * @param CategorieBlogRepository $catRipo
     */
    public function __construct(EntityManagerInterface $em, BlogRepository $blogRipo, CategorieBlogRepository $catRipo)
    {
        $this->em = $em;
        $this->blogRipo = $blogRipo;
        $this->catRipo = $catRipo;
    }


    #[Route('/', name: 'app_admin_blog')]
    public function index(): Response
    {
        return $this->render('admin/admin_blog/index.html.twig', [
            'categories'=>$this->catRipo->findBy(['etat'=>'VALIDE']),
            'articles'=>$this->blogRipo->findAll()
        ]);
    }

    #[Route('/add', name: 'app_admin_blog_add', methods: 'POST')]
    public function add(Request $request)
    {
        $data = $request->request;

        $blog = new Blog();
        $blog -> setSlug(uniqid('blg-'));
        $blog -> setEtat('PUBLIC');
        $blog -> setDate(new \DateTime());
        $blog -> setAuteur($this->getUser());
        $blog -> setTitre($data->get('titre'));
        $blog -> setDescription($data->get('description'));
        $blog -> setContenu($data->get('contenu'));

        $blog -> setCategorie($this->catRipo->find($data->get('categorie')));

        $img=$request->files->get("image");
        $imageName=uniqid().'.'.$img->guessExtension();
        $img->move($this->getParameter("blog"),$imageName);
        $blog->setImage($imageName);

        $this->em->persist($blog);
        $this->em->flush();

        if(in_array("ROLE_ADMIN", $this->getUser()->getRoles())){
            return $this->redirectToRoute('app_admin_blog');
        } elseif (in_array("ROLE_CLIENT", $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_blog');
        }

        return $this->redirectToRoute('app_admin_blog');


    }
}
