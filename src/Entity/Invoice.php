<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\InvoiceRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\DateTime;


use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * @ORM\Entity(repositoryClass=InvoiceRepository::class)
 * @ApiResource(
 * 
 * normalizationContext = {
 *      "groups" = {"invoices_read"}
 * },
 * 
 * denormalizationContext = {
 *      "disable_type_enforcement" = true
 * },
 * 
 * subresourceOperations = {
 *      "api_customers_invoices_get_subresource" = {
 *              "normalization_context"={ "groups" = { "invoices_subresource" }}
 *      }
 * },
 * 
 * attributes = {
 *      "pagination_enabled" = true,
 *      "order": {"sendAt": "desc"}
 * },
 * 
 * itemOperations={"GET","PUT","DELETE","increment"={
 *         "method"="post",
 *         "path"="/invoices/{id}/increment",
 *         "controller"="App\Controller\InvoiceIncrementationController",
 *         "swagger_context"={
 *              "summary"="increment une facture",
 *              "description"="Increment une facture donnee"
 *          }
 *      }
 *    }
 * )
 * 
 * @ApiFilter(OrderFilter::class, properties={"amount", "sendAt"})
 */

class Invoice
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"invoices_read", "customers_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Groups({"invoices_read","invoices_subresource"})
     * @Assert\Type(type="numeric", message="le prix doit être un nombre")
     * @Assert\NotBlank(message="le prix est obligatoire")
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"invoices_read" , "customers_read","invoices_subresource"})
     * @Assert\Type("DateTime")
     * @Assert\NotBlank(message="la date doit être renseigné")
     */
    private $sendAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"invoices_read" , "customers_read"})
     * @Assert\Choice(choices={"SEND","PAID","CANCELED"}, message="Le status n'est pas conforme")
     * @Assert\NotBlank(message="le status doit être renseigné")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="invoices")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"invoices_read"})
     * @Assert\NotBlank(message="le client de le la facture doit être renseigné")
     * 
     */
    private $customer;

    /**
     * @ORM\Column(type="float")
     * @Groups({"invoices_read", "customers_read", "invoices_subresource"})
     * @Assert\NotBlank(message="le chrono n'est pas référencé")
     * @Assert\Type(
     *     type="numeric",
     *     message="La valeur {{ value }} n'est pas conforme au type atttendu : {{ type }}."
     * )
     */
    private $chrono;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupére l'User lié aux factures invoices
     * @Groups({"invoices_read", "invoices_subresource"})
     * @return User
     */
    public function getUser(): User
    {
        return $this->customer->getUser();
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSendAt(): ?\DateTimeInterface
    {
        return $this->sendAt;
    }

    public function setSendAt($sendAt): self
    {
        $this->sendAt = $sendAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getChrono(): ?int
    {
        return $this->chrono;
    }

    public function setChrono($chrono): self
    {
        $this->chrono = $chrono;

        return $this;
    }
}
