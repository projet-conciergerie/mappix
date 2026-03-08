<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Service;
use App\Entity\Category;
use App\Entity\Localisation;
use App\Entity\Evenement;
use App\Entity\Reservation;
use App\Entity\Avis;
use App\Entity\Favoris;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        $users = [];

        // ADMIN
        $admin = new User();
        $admin->setEmail("admin@demo.fr");
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setNom("Admin");
        $admin->setPrenom("Super");
        $admin->setPseudo("superadmin");
        $admin->setLangue("fr");
        $admin->setCreatedAt(new \DateTimeImmutable());
        $admin->setLastConnectedAt(new \DateTimeImmutable());
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, "admin123")
        );

        $manager->persist($admin);

        // TOURISME
        $tourisme = new User();
        $tourisme->setEmail("tourisme@demo.fr");
        $tourisme->setRoles(["ROLE_TOURISME"]);
        $tourisme->setNom("Office");
        $tourisme->setPrenom("Tourisme");
        $tourisme->setPseudo("tourisme");
        $tourisme->setLangue("fr");
        $tourisme->setCreatedAt(new \DateTimeImmutable());
        $tourisme->setLastConnectedAt(new \DateTimeImmutable());
        $tourisme->setPassword(
            $this->passwordHasher->hashPassword($tourisme, "tourisme123")
        );

        $manager->persist($tourisme);

        // USERS
        for ($i = 1; $i <= 10; $i++) {

            $user = new User();
            $user->setEmail("user$i@demo.fr");
            $user->setRoles(["ROLE_USER"]);
            $user->setNom("Nom$i");
            $user->setPrenom("Prenom$i");
            $user->setPseudo("user$i");
            $user->setLangue("fr");
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setLastConnectedAt(new \DateTimeImmutable());

            $user->setPassword(
                $this->passwordHasher->hashPassword($user, "password")
            );

            $manager->persist($user);

            $users[] = $user;
        }

        /*
        LOCALISATIONS
        */

        $loc1 = new Localisation();
        $loc1->setNom("Office de tourisme");
        $loc1->setAdresse("1 place centrale");
        $loc1->setTelephone("0102030405");
        $loc1->setEmail("contact@tourisme.fr");
        $loc1->setPosition("48.8566,2.3522");

        $manager->persist($loc1);

        $loc2 = new Localisation();
        $loc2->setNom("Musée historique");
        $loc2->setAdresse("12 rue du musée");
        $loc2->setTelephone("0102030406");
        $loc2->setEmail("musee@ville.fr");
        $loc2->setPosition("48.8570,2.3510");

        $manager->persist($loc2);

        $loc3 = new Localisation();
        $loc3->setNom("Parc central");
        $loc3->setAdresse("Parc municipal");
        $loc3->setTelephone("0102030407");
        $loc3->setEmail("parc@ville.fr");
        $loc3->setPosition("48.8580,2.3530");

        $manager->persist($loc3);

        /*
        SERVICES
        */

        $service1 = new Service();
        $service1->setNom("Location de vélos");
        $service1->setDescription("Location de vélos pour visiter la ville.");
        $service1->setHours("9h - 18h");
        $service1->setPmr(true);
        $service1->setCreatedAt(new \DateTimeImmutable());
        $service1->setLocalisation($loc1);

        $manager->persist($service1);

        $service2 = new Service();
        $service2->setNom("Visite guidée du musée");
        $service2->setDescription("Visite guidée du musée historique.");
        $service2->setHours("10h - 17h");
        $service2->setPmr(true);
        $service2->setCreatedAt(new \DateTimeImmutable());
        $service2->setLocalisation($loc2);

        $manager->persist($service2);

        $service3 = new Service();
        $service3->setNom("Parcours nature");
        $service3->setDescription("Découverte nature dans le parc.");
        $service3->setHours("Toute la journée");
        $service3->setPmr(false);
        $service3->setCreatedAt(new \DateTimeImmutable());
        $service3->setLocalisation($loc3);

        $manager->persist($service3);

        /*
        CATEGORIES
        */

        $cat1 = new Category();
        $cat1->setNom("Culture");
        $cat1->setService($service2);

        $manager->persist($cat1);

        $cat2 = new Category();
        $cat2->setNom("Sport");
        $cat2->setService($service1);

        $manager->persist($cat2);

        $cat3 = new Category();
        $cat3->setNom("Nature");
        $cat3->setService($service3);

        $manager->persist($cat3);

        /*
        EVENEMENTS SUPPLEMENTAIRES
        */

        $extraEvents = [
            [
                "nom" => "Balade historique du centre-ville",
                "date" => "2026-03-15 10:00",
                "loc" => $loc1,
                "categories" => [$cat1]
            ],
            [
                "nom" => "Découverte du patrimoine local",
                "date" => "2026-03-20 14:00",
                "loc" => $loc2,
                "categories" => [$cat1]
            ],
            [
                "nom" => "Sortie nature guidée",
                "date" => "2026-03-24 09:30",
                "loc" => $loc3,
                "categories" => [$cat3]
            ],
            [
                "nom" => "Tour de la ville à vélo",
                "date" => "2026-03-29 10:00",
                "loc" => $loc1,
                "categories" => [$cat2]
            ],
            [
                "nom" => "Atelier découverte du musée",
                "date" => "2026-04-02 11:00",
                "loc" => $loc2,
                "categories" => [$cat1]
            ],
            [
                "nom" => "Observation de la faune au parc",
                "date" => "2026-04-07 16:00",
                "loc" => $loc3,
                "categories" => [$cat3]
            ]
        ];

        $events = [];

        foreach ($extraEvents as $data) {

            $event = new Evenement();

            $start = new \DateTimeImmutable($data["date"]);
            $end = (clone $start)->modify("+2 hours");

            $event->setNom($data["nom"]);
            $event->setPlaces(25);
            $event->setDescription("Événement touristique organisé par l'office de tourisme.");
            $event->setStartAt($start);
            $event->setEndAt($end);
            $event->setLocalisation($data["loc"]);

            foreach ($data["categories"] as $category) {
                $event->addCategory($category);
            }

            $events[] = $event;
            
            $manager->persist($event);
        }

        /*
        RESERVATIONS
        */

        foreach ($users as $index => $user) {

            $reservation = new Reservation();
            $reservation->setUser($user);
            $reservation->setEvenement($events[$index % count($events)]);

            $manager->persist($reservation);
        }

        /*
        FAVORIS
        */

        foreach ($users as $user) {

            $fav = new Favoris();
            $fav->setUser($user);
            $fav->setService($service1);

            $manager->persist($fav);
        }

        /*
        AVIS
        */

        foreach ($users as $user) {

            $avis = new Avis();
            $avis->setUser($user);
            $avis->setService($service1);
            $avis->setNotation(rand(3, 5));
            $avis->setMessage("Très bon service pour découvrir la ville.");

            $manager->persist($avis);
        }

        /*
        CONTACTS (messages utilisateurs)
        */

        $contactsData = [

            [
                "email" => "visiteur1@email.com",
                "type" => "information",
                "message" => "Bonjour, je souhaiterais savoir si la visite guidée du musée est disponible en anglais."
            ],
            [
                "email" => "famille.touriste@email.com",
                "type" => "reservation",
                "message" => "Nous sommes une famille de 4 personnes. Est-il possible de réserver des places pour l'événement du 22 mars ?"
            ],
            [
                "email" => "marie.dupont@email.com",
                "type" => "information",
                "message" => "Les parcours nature dans le parc sont-ils adaptés aux enfants de moins de 10 ans ?"
            ],
            [
                "email" => "paul.voyage@email.com",
                "type" => "probleme",
                "message" => "Bonjour, je rencontre un problème lors de la réservation d'un événement sur votre site."
            ],
            [
                "email" => "touriste.espagne@email.com",
                "type" => "information",
                "message" => "Hola, ¿ofrecen visitas guiadas en español?"
            ]

        ];

        foreach ($contactsData as $data) {

            $contact = new \App\Entity\Contact();

            $contact->setEmail($data["email"]);
            $contact->setType($data["type"]);
            $contact->setMessage($data["message"]);

            $manager->persist($contact);
        }

        $manager->flush();
    }
}