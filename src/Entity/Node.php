<?php

namespace App\Entity;

use App\Repository\NodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: NodeRepository::class)]
class Node
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private ?Uuid $id;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?string $data = null;

    #[ORM\ManyToMany(targetEntity: File::class, inversedBy: 'nodes', cascade: ['persist'])]
    private Collection $files;

    public function __construct(?Uuid $id = null)
    {
        $this->id = $id ?? Uuid::v4();
        $this->files = new ArrayCollection();
    }

    public static function fromArray(array $data): self
    {
        $node = new Node();
        $node->fill($data);

        return $node;
    }

    public function fill(array $data): self
    {
        $this->setData($data['data']);

        return $this;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return Collection<int, File>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        $this->files->removeElement($file);

        return $this;
    }
}
