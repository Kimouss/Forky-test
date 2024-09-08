<?php

namespace App\Tests\Controller;

use App\Entity\Permission;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PermissionControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/permission/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Permission::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Permission index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'permission[baseRoute]' => 'Testing',
            'permission[access]' => 'Testing',
            'permission[profile]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Permission();
        $fixture->setBaseRoute('My Title');
        $fixture->setAccess('My Title');
        $fixture->setProfile('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Permission');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Permission();
        $fixture->setBaseRoute('Value');
        $fixture->setAccess('Value');
        $fixture->setProfile('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'permission[baseRoute]' => 'Something New',
            'permission[access]' => 'Something New',
            'permission[profile]' => 'Something New',
        ]);

        self::assertResponseRedirects('/permission/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getBaseRoute());
        self::assertSame('Something New', $fixture[0]->getAccess());
        self::assertSame('Something New', $fixture[0]->getProfile());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Permission();
        $fixture->setBaseRoute('Value');
        $fixture->setAccess('Value');
        $fixture->setProfile('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/permission/');
        self::assertSame(0, $this->repository->count([]));
    }
}
