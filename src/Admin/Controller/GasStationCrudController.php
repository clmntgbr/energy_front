<?php

namespace App\Admin\Controller;

use App\Admin\Filter\GasStationStatusFilter;
use App\Entity\GasStation;
use App\Lists\GasStationStatusReference;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Vich\UploaderBundle\Form\Type\VichImageType;

class GasStationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GasStation::class;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addWebpackEncoreEntry('admin');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['updatedAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('gasStationId')
            ->add('pop')
            ->add(GasStationStatusFilter::new('status'))
            ->add(TextFilter::new('address'))
            ->add(DateTimeFilter::new('createdAt'))
            ->add(DateTimeFilter::new('updatedAt'))
            ->add(DateTimeFilter::new('closedAt'));
    }

    public function configureFields(string $pageName): iterable
    {
        $gasStationStatus = [
            GasStationStatusReference::UPDATED_TO_ADDRESS_FORMATED => GasStationStatusReference::UPDATED_TO_ADDRESS_FORMATED,
            GasStationStatusReference::UPDATED_TO_FOUND_IN_TEXTSEARCH => GasStationStatusReference::UPDATED_TO_FOUND_IN_TEXTSEARCH,
            GasStationStatusReference::UPDATED_TO_FOUND_IN_DETAILS => GasStationStatusReference::UPDATED_TO_FOUND_IN_DETAILS,

            GasStationStatusReference::WAITING_VALIDATION => GasStationStatusReference::WAITING_VALIDATION,

            GasStationStatusReference::CLOSED => GasStationStatusReference::CLOSED,
            GasStationStatusReference::OPEN => GasStationStatusReference::OPEN,
        ];

        return [
            FormField::addPanel('Gas Station Details'),
            IdField::new('gasStationId')
                ->setDisabled()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('hash')
                ->hideOnIndex()
                ->setDisabled()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('name')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('pop')
                ->hideOnIndex()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),

            FormField::addPanel('Status'),
            TextField::new('status')
                ->setDisabled()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            ChoiceField::new('statusAdmin')
                ->setLabel('Change status')
                ->hideOnIndex()
                ->autocomplete()
                ->renderAsNativeWidget()
                ->setChoices($gasStationStatus)
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            ArrayField::new('statusesAdmin')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('Status History')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),

            FormField::addPanel('Prices'),
            CodeEditorField::new('lastGasPricesAdmin')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('LastGasPrices'),
            CodeEditorField::new('previousGasPricesAdmin')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('PreviousGasPrices'),

            FormField::addPanel('GooglePlace'),
            IdField::new('googlePlace.id')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('Id')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            IdField::new('googlePlace.uuid')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('Uuid')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            FormField::addRow(),
            TextField::new('googlePlace.placeId')
                ->hideOnIndex()
                ->setLabel('PlaceId')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.googleId')
                ->hideOnIndex()
                ->setLabel('GoogleId')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.website')
                ->hideOnIndex()
                ->setLabel('Website')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.phoneNumber')
                ->hideOnIndex()
                ->setLabel('PhoneNumber')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.compoundCode')
                ->hideOnIndex()
                ->setLabel('CompoundCode')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.globalCode')
                ->hideOnIndex()
                ->setLabel('GlobalCode')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.googleRating')
                ->hideOnIndex()
                ->setLabel('GoogleRating')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.rating')
                ->hideOnIndex()
                ->setLabel('Rating')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.userRatingsTotal')
                ->hideOnIndex()
                ->setLabel('UserRatingsTotal')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.icon')
                ->hideOnIndex()
                ->setLabel('Icon')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.reference')
                ->hideOnIndex()
                ->setLabel('Reference')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.wheelchairAccessibleEntrance')
                ->hideOnIndex()
                ->setLabel('WheelchairAccessibleEntrance')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.businessStatus')
                ->hideOnIndex()
                ->setLabel('BusinessStatus')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            ArrayField::new('googlePlace.openingHours')
                ->hideOnIndex()
                ->setLabel('OpeningHours')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),

            FormField::addPanel('Address'),
            IdField::new('address.id')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('Id')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            IdField::new('address.uuid')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('Uuid')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            FormField::addRow(),
            TextField::new('address.vicinity')
                ->hideOnIndex()
                ->setLabel('Vicinity')
                ->setColumns('col-sm-12'),
            TextField::new('address.street')
                ->hideOnIndex()
                ->setLabel('Street')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.number')
                ->hideOnIndex()
                ->setLabel('Street Number')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.city')
                ->hideOnIndex()
                ->setLabel('City')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.region')
                ->hideOnIndex()
                ->setLabel('Region')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.postalCode')
                ->hideOnIndex()
                ->setLabel('PostalCode')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.country')
                ->hideOnIndex()
                ->setLabel('Country')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.longitude')
                ->hideOnIndex()
                ->setLabel('Longitude')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.latitude')
                ->hideOnIndex()
                ->setLabel('Latitude')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            DateTimeField::new('address.createdAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget()
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('CreatedAt')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            DateTimeField::new('address.updatedAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget()
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('UpdatedAt')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),

            FormField::addPanel('Gas Station Brand'),
            IdField::new('gasStationBrand.id')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('Id')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('gasStationBrand.uuid')
                ->setDisabled()
                ->hideOnIndex()
                ->setLabel('Uuid')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            FormField::addRow(),
            TextField::new('gasStationBrand.name')
                ->hideOnIndex()
                ->setLabel('Name')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('gasStationBrand.reference')
                ->hideOnIndex()
                ->setLabel('Reference')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),

            FormField::addRow(),
            TextField::new('gasStationBrand.image.name', 'Name')
                ->setDisabled()
                ->setLabel('Image Name')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            TextField::new('gasStationBrand.image.originalName', 'originalName')
                ->setDisabled()
                ->setLabel('Image OriginalName')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            NumberField::new('gasStationBrand.image.size', 'Size')
                ->setDisabled()
                ->setLabel('Image Size')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            TextField::new('gasStationBrand.image.mimeType', 'mimeType')
                ->setDisabled()
                ->setLabel('Image MimeType')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            ArrayField::new('gasStationBrand.image.dimensions', 'Dimensions')
                ->setDisabled()
                ->setLabel('Image Dimensions')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),

            FormField::addRow(),
            TextField::new('gasStationBrand.imageLow.name', 'Name')
                ->setDisabled()
                ->setLabel('Image Low Name')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            TextField::new('gasStationBrand.imageLow.originalName', 'originalName')
                ->setDisabled()
                ->setLabel('Image Low OriginalName')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            NumberField::new('gasStationBrand.imageLow.size', 'Size')
                ->setDisabled()
                ->setLabel('Image Low Size')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            TextField::new('gasStationBrand.imageLow.mimeType', 'mimeType')
                ->setDisabled()
                ->setLabel('Image Low MimeType')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            ArrayField::new('gasStationBrand.imageLow.dimensions', 'Dimensions')
                ->setDisabled()
                ->setLabel('Image Low Dimensions')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),

            FormField::addPanel('Gas Station Services'),
            CollectionField::new('gasServices')
                ->setDisabled()
                ->hideOnIndex(),

            FormField::addPanel('Gas Station Metadata'),
            DateTimeField::new('createdAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget()
                ->setDisabled()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            DateTimeField::new('updatedAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->setDisabled(),
            DateTimeField::new('closedAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),

            FormField::addPanel('Image'),
            TextField::new('imageFile', 'Upload')
                ->setFormType(VichImageType::class)
                ->onlyOnForms(),
            ImageField::new('image.name', 'Image')
                ->setRequired(true)
                ->setBasePath('/images/gas_stations/')
                ->hideOnForm(),
            TextField::new('image.name', 'Name')
                ->setDisabled()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            TextField::new('image.originalName', 'originalName')
                ->setDisabled()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            NumberField::new('image.size', 'Size')
                ->setDisabled()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            TextField::new('image.mimeType', 'mimeType')
                ->setDisabled()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),
            ArrayField::new('image.dimensions', 'Dimensions')
                ->setDisabled()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3')
                ->hideOnIndex(),

            FormField::addPanel('Max Retry'),
            Field::new('maxRetryPositionStack')
                ->setDisabled(),
            Field::new('maxRetryTextSearch')
                ->setDisabled(),
            Field::new('maxRetryPlaceDetails')
                ->setDisabled(),

            FormField::addPanel('Json fields'),
            CodeEditorField::new('elementAdmin')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('Element'),
            CodeEditorField::new('address.positionStackApiResultAdmin')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('positionStackApiResult'),
            CodeEditorField::new('googlePlace.textsearchApiResultAdmin')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('TextsearchApiResult'),
            CodeEditorField::new('googlePlace.placeDetailsApiResultAdmin')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('PlaceDetailsApiResult'),
        ];
    }
}
