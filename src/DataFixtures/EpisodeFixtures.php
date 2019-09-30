<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class EpisodeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Link this to the podcast reference from the PodcastFixtures
        for ($i = 0; $i < 5; $i++) {
            $episode = new Episode();
            $episode->setTitle("Sample Episode $i");
            $episode->setDescription(null); // Not Needed but we keep it here as a sample showing the possible options
            $episode->setEpisodeNumber($i + 1); // +1 because numbers dont really start at 0 do they?
            $episode->setDownloadUrl("https://localhost/files/episode/$i"); // Sample URL. IRL we'd use a slug
            $episode->setPodcast($this->getReference(PodcastFixtures::SAMPLE_PODCAST_REFERENCE)); // Set Podcast Relation
            $manager->persist($episode);
        }
        $manager->flush();
    }
}
