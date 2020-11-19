<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Invoice;
use App\Entity\Customer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * Encodeur de mots de passe
     *
     * @var UserPasswordEncoderInterface
     */
    private $encoder;


    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');
       
   
        for($u = 0; $u < 10; $u++){

          
            $user = new User();

            $encoded = $this->encoder->encodePassword($user, "password");

            $user->setFirstName($faker->firstName())
                 ->setLastName($faker->lastName)
                 ->setEmail($faker->email)
                 ->setPassword($encoded);

                 $manager->persist($user);

            for ($c = 0; $c < mt_rand(5,20); $c++) {
                $chrono = 1;
                $customer = new Customer();
                $customer->setFirstName($faker->firstName())
                    ->setLastName($faker->lastName)
                    ->setCompany($faker->company)
                    ->setEmail($faker->email)
                    ->setUser($user);


                $manager->persist($customer);

                for ($i = 0; $i < mt_rand(2, 5); $i++) {
                    $invoice = new Invoice();
                    $invoice->setAmount($faker->numberBetween($min = 1000, $max = 9000))
                        ->setSendAt($faker->dateTimeBetween('-6 months'))
                        ->setStatus($faker->randomElement(['SENT', 'PAID', 'CANCELLED']))
                        ->setCustomer($customer)
                        ->setChrono($chrono);

                    $chrono++;

                    $manager->persist($invoice);
                }
            }
        }

    
        $manager->flush();
    }
}
