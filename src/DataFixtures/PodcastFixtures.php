<?php

namespace App\DataFixtures;

use App\Entity\Podcast;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class PodcastFixtures extends Fixture
{
    public const SAMPLE_PODCAST_REFERENCE = 'sample-podcast-for-episodes';

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 5; $i++) {
            $podcast = new Podcast();
            $podcast->setName("Sample Podcast $i");
            $podcast->setCreatedAt(new \DateTime());
            $podcast->setUpdatedAt(new \DateTime());
            $manager->persist($podcast);
        }

        // Create another podcast that we can use to link episodes to.
        // This way we can load some samples via fixtures and ensure the relationship functions via the API
        $podcast = new Podcast();
        $podcast->setName("Sample Podcast For Episodes");
        $manager->persist($podcast);
        $manager->flush();

        // Create a Fixture Reference for the podcast
        $this->addReference(self::SAMPLE_PODCAST_REFERENCE, $podcast);
    }
}
