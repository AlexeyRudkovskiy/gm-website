<?php


namespace App\Services;


use Symfony\Component\Security\Core\Security;

class SidebarSection
{

    protected $sectionName;

    /** @var array */
    protected $items;

    /**
     * SidebarSection constructor.
     */
    public function __construct()
    {
        $this->items = [];
    }

    /**
     * @return mixed
     */
    public function getSectionName()
    {
        return $this->sectionName;
    }

    /**
     * @param mixed $sectionName
     * @return SidebarSection
     */
    public function setSectionName($sectionName): self
    {
        $this->sectionName = $sectionName;
        return $this;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array $items
     * @return SidebarSection
     */
    public function setItems(array $items): self
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @param string $target
     * @param string $text
     * @param int $badge
     * @param array $aliases
     * @param string $role
     * @return SidebarSection
     */
    public function addItem($target, $text, $badge = 0, $aliases = [], $role = 'ROLE_USER')
    {
        array_push($this->items, [
            'target' => $target,
            'text' => $text,
            'badge' => $badge,
            'aliases' => $aliases,
            'role' => $role
        ]);

        return $this;
    }

    /**
     * @param array $roles
     * @return SidebarSection
     */
    public function filter(array $roles): SidebarSection
    {
        $this->items = array_filter($this->items, function ($item) use ($roles) {
            return in_array($item['role'] ?? 'ROLE_USER', $roles);
        });

        return $this;
    }

}
