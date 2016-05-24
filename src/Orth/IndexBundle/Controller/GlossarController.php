<?php

namespace Orth\IndexBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Orth\IndexBundle\Entity\Glossar;

class GlossarController extends Controller
{
    public function glossarAction(Request $request, $letter) {
        
        $em = $this->getDoctrine()->getManager();
        
        if($request->query->get('q')) {
            $searchTerm = $request->query->get('q');

            $query = $em->createQuery( "SELECT g FROM OrthIndexBundle:Glossar g WHERE g.subject LIKE :searchterm OR g.description LIKE :searchterm ORDER BY g.id")
                    ->setParameter(':searchterm', '%'.$searchTerm.'%');
        } elseif ($letter != NULL) {
            $query = $em->createQuery( "SELECT g FROM OrthIndexBundle:Glossar g WHERE g.letter = :letter ORDER BY g.id")
                    ->setParameter(':letter', $letter);
        }
        else {
            $query = $em->createQuery( "SELECT g FROM OrthIndexBundle:Glossar g ORDER BY g.id")
                    ->setMaxResults(20);
        }
        
        $results = $query->getResult();
        
        return $this->render('OrthIndexBundle:Index:glossar.html.twig', array('results' => $results));
    }
    
    public function detailAction(Request $request, $string) {
        
        $em = $this->getDoctrine()->getManager();
        
        $id = explode("-", $string);
                         
        $glossar = new Glossar();
        
        $result = $em->getRepository('OrthIndexBundle:Glossar')->findOneBy(array('id' => $id[0]));
               
        return $this->render('OrthIndexBundle:Index:detail.html.twig', array('result' => $result));
        
    }
    
}
