<?php

namespace App\Controller\Admin\Filter;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDataDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\FilterTrait;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\TextFilterType;

class ItemFilter implements FilterInterface
{
    use FilterTrait;

    /**
     * @param string $propertyName
     * @param string|null $label
     * @return ItemFilter
     */
    public static function new(string $propertyName, string $label = null): self
    {
        return (new self())
            ->setFilterFqcn(__CLASS__)
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(TextFilterType::class);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param FilterDataDto $filterDataDto
     * @param FieldDto|null $fieldDto
     * @param EntityDto $entityDto
     */
    public function apply(QueryBuilder $queryBuilder, FilterDataDto $filterDataDto, ?FieldDto $fieldDto, EntityDto $entityDto): void
    {
        $comparison = $filterDataDto->getComparison();
        $client = $filterDataDto->getValue();
        $parameterName = $filterDataDto->getParameterName();

        $queryBuilder->innerJoin('entity.item', 'i')
            ->where(sprintf('%s.%s %s :%s', 'i', 'name', $comparison, $parameterName))
        ;

        $queryBuilder
            ->setParameter($parameterName, $client)
        ;
    }
}
