<?php

namespace Orth\IndexBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orth\IndexBundle\Entity\Articles;
use Orth\IndexBundle\Entity\Searchindex;
use Symfony\Component\HttpFoundation\Request;
use Orth\IndexBundle\Form\Type\SearchType;
use Orth\IndexBundle\Classes\GermanStemmer;
use Orth\IndexBundle\Classes\ExtractCommonWords;

class IndexController extends Controller
{
    public function indexAction()
    {

        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('OrthIndexBundle:Index:index.html.twig', array('user' => $user));

    }
    public function mailSuccessAction()
    {
        return $this->render('OrthIndexBundle:Mail:orderSuccessMail.html.twig');

    }    public function searchAction(Request $request) {

        $products = new Products();

        $products = $this->getDoctrine()
            ->getRepository('OrthIndexBundle:Articles');

        if ( !$products ) {
            throw $this->createNotFoundException(
                'Keine Termine gefunden!');
            }


        if ($request->query->get('q')) {

           $string = "+" . str_replace("-", "*+", str_replace(" ", " +", $request->query->get('q')));

            $queryProducts = $products->createQueryBuilder('p')
                ->where('MATCH (p.beschreibung1, p.beschreibung2, p.beschreibung3, p.tags) AGAINST (:string BOOLEAN) > 0 GROUP BY p.mainNumber ORDER BY p.id ASC')
                ->setParameter('string', $string)
                ->setMaxResults(40)
                ->getQuery();

            $products = $queryProducts->getResult();
                        $url = $this->get('router')->generate('orth_index_search', array('slug' => $string));

            }

        return $this->render('OrthIndexBundle:Index:results.html.twig', array('products' => $products));

    }

    public function articledetailAction($slug) {

        $product = new Products();

        $product = $this->getDoctrine()
            ->getRepository('OrthIndexBundle:Articles');

        if ( !$product ) {
            throw $this->createNotFoundException(
                'Keine Termine gefunden!');
            }

            $string = $slug;

            $queryProduct = $product->createQueryBuilder('p')
                ->where('p.mainNumber = :string')
                ->setParameter('string', $string)
                ->setMaxResults(1)
                ->getQuery();

            $product = $queryProduct->getResult();

            return $this->render('OrthIndexBundle:Index:article_detail.html.twig', array('product' => $product));


            }


    #
    # Routing for general static sites
    #

    public function accountAction() {
        return $this->render('OrthIndexBundle:Index:account.html.twig');
    }


    public function impressumAction() {
        return $this->render('OrthIndexBundle:Index:impressum.html.twig');
    }

    public function agbAction() {
        return $this->render('OrthIndexBundle:Index:agb.html.twig');
    }

    public function datenschutzAction() {
        return $this->render('OrthIndexBundle:Index:datenschutz.html.twig');
    }

    public function widerrufAction() {
        return $this->render('OrthIndexBundle:Index:widerruf.html.twig');
    }

    public function arbeitsschutzAction() {
        return $this->render('OrthIndexBundle:Index:arbeitsschutz.html.twig');
    }

    public function berufsbekleidungAction() {
        return $this->render('OrthIndexBundle:Index:berufsbekleidung.html.twig');
    }

    public function cteilemanagementAction() {
        return $this->render('OrthIndexBundle:Index:cteilemanagement.html.twig');
    }

    public function werkzeugtechnikAction() {
        return $this->render('OrthIndexBundle:Index:werkzeugtechnik.html.twig');
    }

    public function hygieneartikelAction() {
        return $this->render('OrthIndexBundle:Index:hygieneartikel.html.twig');
    }

    public function unternehmenAction() {
        return $this->render('OrthIndexBundle:Index:unternehmen.html.twig');
    }

    public function teamAction() {
        return $this->render('OrthIndexBundle:Index:team.html.twig');
    }

    public function dienstleistungenAction() {
        return $this->render('OrthIndexBundle:Index:dienstleistungen.html.twig');
    }

    public function logoemblemserviceAction() {
        return $this->render('OrthIndexBundle:Index:logoemblemservice.html.twig');
    }

    public function kontaktAction(Request $request) {

        $form = $this->createFormBuilder()
                ->add('firstname', 'text', array('label' => false, 'attr' => array('class' => 'form-control')))
                ->add('lastname', 'text', array('label' => false, 'attr' => array('class' => 'form-control')))
                ->add('email', 'email', array('label' => false, 'attr' => array('class' => 'form-control')))
                ->add('subject', 'text', array('label' => false, 'attr' => array('class' => 'form-control')))
                ->add('message', 'textarea', array('label' => false, 'attr' => array('class' => 'form-control', 'style' => 'height: 200px; resize: none;')))
                ->add('save', 'submit', array('label' => 'Absenden', 'attr' => array('class' => 'btn btn-primary')))
                ->getForm();


        $form->handleRequest($request);

        $formData = $form->getData();

        if ($form->isValid()) {
            $message = \Swift_Message::newInstance()
                ->setSubject('Kontaktformular')
                ->setFrom('no-reply@ute-orth.de')
                ->setTo('info@ute-orth.de')
                ->setContentType("text/html")
                ->setBody(
                    $this->renderView(
                        'OrthIndexBundle:Mail:kontaktform.html.twig',
                        array('firstname' => $formData['firstname'], 'lastname' => $formData['lastname'], 'email' => $formData['email'], 'subject' => $formData['subject'], 'message' => $formData['message']),
                    'text/html'
                    )
                );

            $this->get('mailer')->send($message);

            $this->get('session')->getFlashBag()->add('notice', 'Nachricht wurde erfolgreich versendet!');
        }


        return $this->render('OrthIndexBundle:Index:kontakt.html.twig', array('form' => $form->createView()));
    }
}
