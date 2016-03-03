<?php

namespace Orth\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Orth\IndexBundle\Entity\Categories;

use Orth\AdminBundle\Form\Type\CategoryType;
use Orth\AdminBundle\Form\Type\MoveArticlesType;

class CategoriesController extends Controller
{
    public function categoriesAction()
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $categories = $em->getRepository('OrthIndexBundle:Categories')->findBy(array('parentId' => NULL));
        
        return $this->render('OrthAdminBundle:Categories:categories.html.twig', array('categories' => $categories));
        
    }
    
    public function rootAction($id, Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $category = $em->getRepository('OrthIndexBundle:Categories')->findOneBy(array('id' => $id));
 
        $childs = $em->getRepository('OrthIndexBundle:Categories')->findBy(array('parentId' => $id));

        $form = $this->createForm(new CategoryType(), $category);

        $form->handleRequest($request);

        if ($form->isValid()) {
            
            $em->persist($category);
            $em->flush();
            
        }
        
        return $this->render('OrthAdminBundle:Categories:root.html.twig', array('form' => $form->createView(), 'categories' => $childs, 'category' => $category));
    
    }
    
    public function childAction($id, Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $category = $em->getRepository('OrthIndexBundle:Categories')->findOneBy(array('id' => $id));
 
        $childs = $em->getRepository('OrthIndexBundle:Categories')->findBy(array('parentId' => $id));

        $form = $this->createForm(new CategoryType(), $category);
        $form->add('parent', 'entity', array(
                'class' => 'OrthIndexBundle:Categories',
                'group_by' => 'catForArticleName',
                'property' => 'categoryName',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $repo) {
                     $qb = $repo->createQueryBuilder('c');
                     $qb->where('c.parent IS NULL');
                     $qb->orderBy('c.categoryName', 'ASC');

                     return $qb;
                }
            ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            $em->persist($category);
            $em->flush();
            
        }
        
        return $this->render('OrthAdminBundle:Categories:child.html.twig', array('form' => $form->createView(), 'categories' => $childs, 'category' => $category));
    
    }
    
    public function grandchildAction($id, Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $category = $em->getRepository('OrthIndexBundle:Categories')->findOneBy(array('id' => $id));
 
        $childs = $em->getRepository('OrthIndexBundle:Categories')->findBy(array('parentId' => $id));
        $articles = $em->getRepository('OrthIndexBundle:Articles')->findBy(array('catRef' => $category->getId()));

        $form = $this->createForm(new CategoryType(), $category);
        $form->add('parent', 'entity', array(
                'class' => 'OrthIndexBundle:Categories',
                'group_by' => 'parentName',
                'property' => 'categoryName',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $repo) {
                     $qb = $repo->createQueryBuilder('c');
                     $qb->leftJoin('c.parent', 'c1');
                     $qb->where('c.parent IS NOT NULL');
                     $qb->andWhere('c1.parent IS NULL');
                     $qb->orderBy('c.parent', 'ASC');
                     $qb->orderBy('c1.categoryName', 'ASC');
                     
                     return $qb;
                }
            ));
            
        $form2 = $this->createForm(new MoveArticlesType(), $articles);
   
        $form->handleRequest($request);
        $form2->handleRequest($request);
        if ($form->isValid()) {
            
            $em->persist($category);
            $em->flush();
            
        }
        if ($request->request->get('article')) {
            $articlesToMove = $request->request->get('article');
            $category = $em->getRepository('OrthIndexBundle:Categories')->findOneBy(array('id' => $articlesToMove['category']));
            foreach ($articlesToMove['check'] as $articleId) {
                $article = $em->getRepository('OrthIndexBundle:Articles')->findOneBy(array('id' => $articleId));
                $article->setCategory($category);
                $em->persist($article);
                $em->flush();
            }
        }        
        
        return $this->render('OrthAdminBundle:Categories:grandchild.html.twig', array('form' => $form->createView(), 'form2' => $form2->createView(), 'categories' => $childs, 'category' => $category, 'articles' => $articles));
    
    }
    
    public function deletecategoryAction($id)
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $emCategories = $em->getRepository('OrthIndexBundle:Categories');
        $emArticles = $em->getRepository('OrthIndexBundle:Articles');
        
        $category = $emCategories->findOneBy(array('id' => $id));
        $hasChildren = $emCategories->checkForChildrenCategories($id);
        $childCategories = $emCategories->getAllChildCategories($id);
        $articles = $emArticles->getArticlesByCategory($id);
        
        if ( $hasChildren ) {
            $this->get('session')->getFlashBag()->add('danger', 'Die Kategorie hat noch Unterkategorien! Löschen nicht möglich!');
        } elseif (!$hasChildren AND $articles) {
            $this->get('session')->getFlashBag()->add('danger', 'Die Kategorie beinhaltet Artikel! Löschen nicht möglich!');
        } else {
            $this->get('session')->getFlashBag()->add('success', 'Die Kategorie wurde erfolgreich gelöscht !');
            $em->remove($category);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl('orth_admin_categories'));
    }
    
    public function newrootAction(Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $category = new Categories();
                
        $form = $this->createForm(new CategoryType(), $category);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            $category->setParent(NULL);
            $em->persist($category);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Die Kategorie wurde erfolgreich hinzugefügt!');
            
        }
        
        return $this->render('OrthAdminBundle:Categories:newroot.html.twig', array('form' => $form->createView()));
    }
    
    public function newchildAction($parent, Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $parentCategory = $em->getRepository('OrthIndexBundle:Categories')->findOneBy(array('id' => $parent));
        $category = new Categories();
                
        $form = $this->createForm(new CategoryType(), $category);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            $category->setParent($parentCategory);
            $em->persist($category);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Die Kategorie wurde erfolgreich hinzugefügt!');
            
        }

        return $this->render('OrthAdminBundle:Categories:new.html.twig', array('form' => $form->createView()));
    }
    
    public function moveToCategoryAction(Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $category = new Categories();

        
        $this->get('session')->getFlashBag()->add('success', 'Die Artikel wurden erfolgreich verschoben!');

        return $this->render('OrthAdminBundle:Categories:new.html.twig', array('form' => $form->createView()));
    }
}
