<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Emprunt;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testGetIdReturnsNullByDefault(): void
    {
        $this->assertNull($this->user->getId());
    }

    public function testSetAndGetEmail(): void
    {
        $email = 'user@example.com';
        $result = $this->user->setEmail($email);
        
        $this->assertSame($this->user, $result);
        $this->assertSame($email, $this->user->getEmail());
    }

    public function testGetUserIdentifier(): void
    {
        $email = 'user@example.com';
        $this->user->setEmail($email);
        
        $this->assertSame($email, $this->user->getUserIdentifier());
    }

    public function testSetAndGetPassword(): void
    {
        $password = 'hashed_password_123';
        $result = $this->user->setPassword($password);
        
        $this->assertSame($this->user, $result);
        $this->assertSame($password, $this->user->getPassword());
    }

    public function testGetRolesContainsRoleUserByDefault(): void
    {
        $roles = $this->user->getRoles();
        
        $this->assertContains('ROLE_USER', $roles);
    }

    public function testSetAndGetRoles(): void
    {
        $roles = ['ROLE_ADMIN', 'ROLE_LIBRARIAN'];
        $result = $this->user->setRoles($roles);
        
        $this->assertSame($this->user, $result);
        
        $returnedRoles = $this->user->getRoles();
        $this->assertContains('ROLE_USER', $returnedRoles);
        $this->assertContains('ROLE_ADMIN', $returnedRoles);
        $this->assertContains('ROLE_LIBRARIAN', $returnedRoles);
    }

    public function testGetRolesReturnsUniqueValues(): void
    {
        $this->user->setRoles(['ROLE_ADMIN', 'ROLE_USER', 'ROLE_ADMIN']);
        
        $roles = $this->user->getRoles();
        
        $this->assertSame(count($roles), count(array_unique($roles)));
    }

    public function testRoleConstants(): void
    {
        $this->assertSame('ROLE_ADMIN', User::ROLE_ADMIN);
        $this->assertSame('ROLE_LIBRARIAN', User::ROLE_LIBRARIAN);
    }

    public function testGetEmpruntsReturnsEmptyCollectionByDefault(): void
    {
        $emprunts = $this->user->getEmprunts();
        
        $this->assertCount(0, $emprunts);
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $emprunts);
    }

    public function testAddEmprunt(): void
    {
        $emprunt = $this->createMock(Emprunt::class);
        $emprunt->expects($this->once())
            ->method('setUser')
            ->with($this->user);
        
        $result = $this->user->addEmprunt($emprunt);
        
        $this->assertSame($this->user, $result);
        $this->assertCount(1, $this->user->getEmprunts());
        $this->assertTrue($this->user->getEmprunts()->contains($emprunt));
    }

    public function testAddEmpruntDoesNotDuplicates(): void
    {
        $emprunt = $this->createMock(Emprunt::class);
        $emprunt->expects($this->once())
            ->method('setUser');
        
        $this->user->addEmprunt($emprunt);
        $this->user->addEmprunt($emprunt);
        
        $this->assertCount(1, $this->user->getEmprunts());
    }

    public function testRemoveEmprunt(): void
    {
        $emprunt = $this->createMock(Emprunt::class);
        $emprunt->method('getUser')->willReturn($this->user);
        $emprunt->expects($this->once())
            ->method('setUser')
            ->with(null);
        
        $this->user->getEmprunts()->add($emprunt);
        $result = $this->user->removeEmprunt($emprunt);
        
        $this->assertSame($this->user, $result);
        $this->assertCount(0, $this->user->getEmprunts());
    }

    public function testEraseCredentials(): void
    {
        // Cette méthode est dépréciée mais doit exister
        $this->user->eraseCredentials();
        
        // Vérifier que la méthode existe et ne lève pas d'erreur
        $this->assertTrue(method_exists($this->user, 'eraseCredentials'));
    }

    public function testSerialize(): void
    {
        $this->user->setEmail('test@example.com');
        $this->user->setPassword('hashed_password');
        
        $serialized = $this->user->__serialize();
        
        $this->assertIsArray($serialized);
        $this->assertArrayHasKey("\0App\Entity\User\0password", $serialized);
        
        // Vérifie que le mot de passe est hashé avec CRC32C
        $passwordKey = "\0App\Entity\User\0password";
        $this->assertNotSame('hashed_password', $serialized[$passwordKey]);
        $this->assertSame(hash('crc32c', 'hashed_password'), $serialized[$passwordKey]);
    }

    public function testUserComplet(): void
    {
        $this->user
            ->setEmail('admin@library.com')
            ->setPassword('hashed_password_123')
            ->setRoles([User::ROLE_ADMIN, User::ROLE_LIBRARIAN]);

        $this->assertSame('admin@library.com', $this->user->getEmail());
        $this->assertSame('hashed_password_123', $this->user->getPassword());
        
        $roles = $this->user->getRoles();
        $this->assertContains(User::ROLE_ADMIN, $roles);
        $this->assertContains(User::ROLE_LIBRARIAN, $roles);
        $this->assertContains('ROLE_USER', $roles);
    }

    public function testFluentInterface(): void
    {
        $result = $this->user
            ->setEmail('test@example.com')
            ->setPassword('password')
            ->setRoles(['ROLE_ADMIN']);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($this->user, $result);
    }
}