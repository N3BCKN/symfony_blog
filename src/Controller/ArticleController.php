<?php

namespace App\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// for database connections
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Article;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends AbstractController
{


    /**
     * @Route("/article/delete/{id}")
     * @Method({"DELETE"})
     */
    public function delete(Request $request, $id)
    {   
        $entityManager = $this->getDoctrine()->getManager();
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('article_index');
    }

    /**
     * @Route("/", name="article_index")
     * @Method({"GET"})
     */
    public function index()
    {   
        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();

        return $this->render('article/index.html.twig', ['articles' => $articles]);
    }

    /**
     * @Route("/article/new", name="new_article")
     * Method({"GET", "POST"})
     */
    public function new(Request $request) {
        $article = new Article();
  
        $form = $this->createFormBuilder($article)
          ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
          ->add('body', TextareaType::class, array(
            'required' => false,
            'attr' => array('class' => 'form-control')
          ))
          ->add('save', SubmitType::class, array(
            'label' => 'Create',
            'attr' => array('class' => 'btn btn-primary mt-3')
          ))
          ->getForm();
  
        $form->handleRequest($request);
  
        if($form->isSubmitted() && $form->isValid()) {
          $article = $form->getData();
  
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->persist($article);
          $entityManager->flush();
  
          return $this->redirectToRoute('article_index');
        }
  
        return $this->render('article/new.html.twig', array(
          'form' => $form->createView()
        ));
      }

    /**
     * @Route("/article/{id}", name="article_show")
     * @Method({"GET"})
     */
    public function show($id){
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        return $this->render('article/show.html.twig', ['article' => $article]);
    }


    /**
     * @Route("/article/edit/{id}", name="edit_article")
     * Method({"GET", "POST"})
     */
    public function edit(Request $request, $id) {
        $article = new Article();
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
  
        $form = $this->createFormBuilder($article)
          ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
          ->add('body', TextareaType::class, array(
            'required' => false,
            'attr' => array('class' => 'form-control')
          ))
          ->add('save', SubmitType::class, array(
            'label' => 'Update',
            'attr' => array('class' => 'btn btn-primary mt-3')
          ))
          ->getForm();
  
        $form->handleRequest($request);
  
        if($form->isSubmitted() && $form->isValid()) {
  
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->flush();
  
          return $this->redirectToRoute('article_index');
        }
  
        return $this->render('article/edit.html.twig', array(
          'form' => $form->createView()
        ));
    }


    /**
     * @Route("/save")
     * @Method({"POST"})
     */
    public function save(){
        $entityManager = $this->getDoctrine()->getManager();

        $article = new Article();
        $article->setTitle("Article title ". rand(5, 200));
        $article->setBody('Article Body: '.rand(5, 200));

        $entityManager->persist($article);

        $entityManager->flush();

        return new Response("<h2>New Article Created!</h2>");
    }

}
