<?php

namespace App\Tests\Controller\Api\v1;

use ApiTestCase\JsonApiTestCase;
use PHPUnit\Framework\AssertionFailedError;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PodcastControllerTest extends JsonApiTestCase
{
    public function testIndex()
    {
        $this->client->request('GET', '/api/v1/podcast/');
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'Api/v1/Podcast/index');
    }

    public function testView()
    {
        $this->client->request('GET', '/api/v1/podcast/6');
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'Api/v1/Podcast/view');
    }

    public function testViewInvalidId()
    {
        $this->client->request('GET', '/api/v1/podcast/101');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isNotFound());
    }

    public function testCreate()
    {
        $data = [
            'name' => 'Minimal Podcast',
        ];

        $this->client->request('POST', '/api/v1/podcast/create', $data);
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'Api/v1/Podcast/create');
    }

    public function testCreateAsJson()
    {
        $data = [
            'name' => 'Full Podcast',
        ];
        $serialized = json_encode($data);

        $this->client->request('POST', '/api/v1/podcast/create', [], [], ['CONTENT_TYPE' => 'application/json'], $serialized);
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'Api/v1/Podcast/create');

    }

    public function testCreateBlankData()
    {
        $this->client->request('POST', '/api/v1/podcast/create');
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'Api/v1/Podcast/create_blank_data');
    }

    public function testUpdateMissingPodcast()
    {
        $this->client->request('PUT', '/api/v1/podcast/101/update');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isNotFound());
    }

    public function testUpdateMissingFields()
    {
        $this->client->request('GET', '/api/v1/podcast/6');
        $old_response = $this->client->getResponse();

        $this->client->request('PUT', '/api/v1/podcast/6/update');
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'Api/v1/Podcast/update_blank_data');
        $this->assertEquals($response->getContent(), $old_response->getContent()); // Nothing Should have changed
    }

    public function testUpdateChangedName()
    {
        $this->client->request('GET', '/api/v1/podcast/6');
        $old_response = $this->client->getResponse();
        $old_entity_data = json_decode($old_response->getContent(), true);

        $changes = [
            'name' => 'Sample Name',
        ];
        $this->client->request('PUT', '/api/v1/podcast/6/update', $changes);
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'Api/v1/Podcast/update_name');
        $this->assertNotEquals($response, $old_response); // Name Should Have Changed

        $updated_entity_data = $old_entity_data;
        $updated_entity_data['result'] = array_merge($updated_entity_data['result'], $changes);
        $new_entity_data = json_decode($response->getContent(), true);

        // UpdatedAt should have updated so lets first check that its changed.
        $this->assertGreaterThan($old_entity_data['result']['updatedAt'], $new_entity_data['result']['updatedAt']);

        // Pop the updatedAt from both items and compare the rest to make sure it all changed the same
        unset($new_entity_data['result']['updatedAt']);
        unset($updated_entity_data['result']['updatedAt']);

        // Compare the rest and make sure its all equal
        $this->assertEquals($new_entity_data, $updated_entity_data);
    }

    public function testDeleteMissingEntity()
    {
        $this->client->request('DELETE', '/api/v1/podcast/101/delete');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isNotFound());
    }

    public function testDeleteValidEntity()
    {
        // Get the Old Record
        $this->client->request('GET', '/api/v1/podcast/6');
        $old_response = $this->client->getResponse();
        $old_entity_data = json_decode($old_response->getContent(), true);

        // Make the Delete Request
        $this->client->request('DELETE', '/api/v1/podcast/6/delete');
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'Api/v1/Podcast/delete_valid');

        // Get the Old Record and ensure it doesn't exist
        $this->client->request('GET', '/api/v1/podcast/6');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isNotFound());
    }

    public function testIndexPaginationSingleItem()
    {
        $this->client->request('GET', '/api/v1/podcast/?limit=1');
        $response = $this->client->getResponse();

        // Decode Response
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(count($data['result']), 1);
    }

    public function testIndexPaginationMultipleItems()
    {
        $this->client->request('GET', '/api/v1/podcast/?limit=3');
        $response = $this->client->getResponse();

        // Decode Response
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(count($data['result']), 3);
    }

    public function testIndexPaginationSecondPage()
    {
        $this->client->request('GET', '/api/v1/podcast/?limit=1&page=2');
        $firstPageResponse = $this->client->getResponse();
        $firstPageData = json_decode($firstPageResponse->getContent(), true);
        $this->assertEquals(count($firstPageData['result']), 1);

        $this->client->request('GET', '/api/v1/podcast/?limit=1');
        $secondPageResponse = $this->client->getResponse();
        $secondPageData = json_decode($secondPageResponse->getContent(), true);
        $this->assertNotEquals($secondPageData['result'], $firstPageData['result']);
    }
}
