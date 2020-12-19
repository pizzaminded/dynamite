<?php
declare(strict_types=1);

namespace Dynamite\Tests\Mapping;


use Doctrine\Common\Annotations\AnnotationReader;
use Dynamite\Mapping\ItemMappingException;
use Dynamite\Mapping\ItemMappingReader;
use Dynamite\Tests\Fixtures\Dummy;
use Dynamite\Tests\Fixtures\DummyItem;
use Dynamite\Tests\Fixtures\DummyItemWithPartitionKeyFormat;
use Dynamite\Tests\Fixtures\Valid\UserActivity;
use PHPUnit\Framework\TestCase;

class ItemMappingReaderTest extends TestCase
{

    private function createItemMappingReader(): ItemMappingReader
    {
        return new ItemMappingReader(new AnnotationReader());
    }


    public function testCheckingForMissingItemAnnotation(): void
    {
        $this->expectException(ItemMappingException::class);
        $this->expectExceptionMessage('Class "Dynamite\Tests\Fixtures\Dummy" does not have "Dynamite\Configuration\Item" annotation given.');

        $parser = $this->createItemMappingReader();
        $parser->getMappingFor(Dummy::class);
    }

    public function testCheckingForMissingPartitionKeyFormatAnnotation(): void
    {
        $this->expectException(ItemMappingException::class);
        $this->expectExceptionMessage('There is no PartitionKeyFormat annotation set in any property of "Dynamite\Tests\Fixtures\DummyItem" class.');

        $parser = $this->createItemMappingReader();
        $parser->getMappingFor(DummyItem::class);
    }

    public function testCheckingForMissingPartitionKeyProp(): void
    {
        $this->expectException(ItemMappingException::class);
        $this->expectExceptionMessage('There is no PartitionKey annotation set in any property of "Dynamite\Tests\Fixtures\DummyItemWithPartitionKeyFormat" class.');

        $parser = $this->createItemMappingReader();
        $parser->getMappingFor(DummyItemWithPartitionKeyFormat::class);
    }


    public function testReadingFullPrimaryKeyMapping(): void
    {
        $parser = $this->createItemMappingReader();
        $mapping = $parser->getMappingFor(UserActivity::class);

        $this->assertEquals('USERACTIVITY#{userId}', $mapping->getPartitionKeyFormat());
        $this->assertEquals('pk', $mapping->getPartitionKeyProperty());

        $this->assertEquals('ACT#{activityId}', $mapping->getSortKeyFormat());
        $this->assertEquals('sk', $mapping->getSortKeyProperty());
    }

    public function testReadingObjectType(): void
    {
        $parser = $this->createItemMappingReader();
        $mapping = $parser->getMappingFor(UserActivity::class);

        $this->assertEquals('USERACTIVITY', $mapping->getObjectType());
    }
}