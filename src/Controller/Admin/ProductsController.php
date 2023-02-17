<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Form\ProductsFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/produits', name: 'admin_products_')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/products/index.html.twig');
    }

    #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // on crée un nouveau produit
        $product = new Products();

        // on crée le formulaire
        $productForm = $this->createForm(ProductsFormType::class, $product);

        // on traite la requête du formulaire
        $productForm->handleRequest($request);

        // on vérifie si le formulaire est soumis et valide
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            // on génère le slug avec le nom du produit
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);

            // on arrondit le prix en faisant *100
            $prix = $product->getPrice() * 100;
            $product->setPrice($prix);

            // on va stocker les informations
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès');

            // on redirige
            return $this->redirectToRoute('admin_products_index');
        }

        return $this->render('admin/products/add.html.twig', [
            'productForm' => $productForm->createView()
        ]);
        // on peut également écrire : 
        // return $this->renderForm('admin/products/add.html.twig', compact('productForm'));
    }

    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Products $product, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);

        // on divise le prix par 100
        $prix = $product->getPrice() / 100;
        $product->setPrice($prix);

        $productForm = $this->createForm(ProductsFormType::class, $product);

        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {

            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);

            $prix = $product->getPrice() * 100;
            $product->setPrice($prix);

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit modifié avec succès');

            return $this->redirectToRoute('admin_products_index');
        }

        return $this->render('admin/products/edit.html.twig', [
            'productForm' => $productForm->createView()
        ]);

}

    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Products $product): Response
    {
        // on vérifie si l'utilisateur peut supprimer avec le voter
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);

        return $this->render('admin/products/index.html.twig');
    }
}
