<?php

namespace App\Controller;

use App\Entity\CategorieBlog;
use App\Repository\BlogRepository;
use App\Repository\CategorieBlogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class AdminBlogCategorieController extends AbstractController
{
    private CategorieBlogRepository $catRipo;
    private EntityManagerInterface $em;

    /**
     * @param BlogRepository $blogRipo
     * @param EntityManagerInterface $em
     */
    public function __construct(CategorieBlogRepository $catRipo, EntityManagerInterface $em)
    {
        $this->catRipo = $catRipo;
        $this->em = $em;
    }


    #[Route('/admin/blog/categorie', name: 'app_admin_blog_categorie')]
    public function index(): Response
    {
        return $this->render('admin/admin_blog_categorie/index.html.twig', [
            'categories'=>$this->catRipo->findAll()
        ]);
    }

    #[Route('/admin/blog/categorie/add', name: 'app_admin_blog_categorie_add')]
    public function add(Request $request)
    {
        $cat = new CategorieBlog();
        $cat -> setLibelle($request->request->get('nom'));
        $cat -> setEtat('VALIDE');
        $cat -> setSlug(uniqid('blog-cat-'));
        $this -> em ->persist($cat);
        $this -> em -> flush();
        return $this -> redirectToRoute('app_admin_blog_categorie');
    }
}
