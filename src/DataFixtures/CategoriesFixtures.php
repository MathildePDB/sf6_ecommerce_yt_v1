<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;


class CategoriesFixtures extends Fixture
{
    private $counter = 1;

    public function __construct(private SluggerInterface $slugger) {}

    public function createCategory(string $name, Categories $parent = null, ObjectManager $manager) {
        $category = new Categories();
        $category->setName($name);
        $category->setSlug($this->slugger->slug($category->getName())->lower());
        $category->setParent($parent);
        $manager->persist($category);

        // référencement de la catégorie pour la stocker sous forme de nombre
        $this->addReference('cat-'.$this->counter, $category);
        $this->counter++;

        return $category;
    }

    public function load(ObjectManager $manager): void
    {
        // // on appelle l'entité Categories
        // $parent = new Categories();
        // // on donne un name à la category
        // $parent->setName('Informatique');
        // // pour le slug c'est identique mais en minuscules
        // $parent->setSlug($this->slugger->slug($parent->getName())->lower());
        // $manager->persist($parent);

       $parent = $this->createCategory('Informatique', null, $manager);
       $this->createCategory('Ordianteur', $parent, $manager);
       $this->createCategory('Ecran', $parent, $manager);
       $this->createCategory('Souris', $parent, $manager);

       $parent = $this->createCategory('Mode', null, $manager);
       $this->createCategory('Homme', $parent, $manager);
       $this->createCategory('Femme', $parent, $manager);
       $this->createCategory('Enfant', $parent, $manager);

        $manager->flush();
    }
}
