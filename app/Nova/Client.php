<?php

namespace App\Nova;

use App\Enums\AreasEnum;
use App\Nova\DocumentType as NovaDocumentType;
use App\Nova\User as NovaUser;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Client extends Resource
{
    /**
     * The pagination per-page options used the resource index.
     *
     * @return array<int, int>|int|null
     */
    public static $perPageOptions = [10, 50, 100, 150];

    /**
     * Get the displayable label of the resource.
     */
    public static function label(): string
    {
        return 'Clientes';
    }

    /**
     * Get the displayable singular label of the resource.
     */
    public static function singularLabel(): string
    {
        return 'Cliente';
    }

    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Client>
     */
    public static $model = \App\Models\Client::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'business_name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'business_name',
        'trade_name',
        'document_number'.'documentType.name',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array<int, \Laravel\Nova\Fields\Field>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Tipo de documento', 'documentType', NovaDocumentType::class)
                ->sortable()
                ->rules('required'),

            Text::make('Número de documento', 'document_number')
                ->readonly(function ($request) {
                    return $request->isUpdateOrUpdateAttachedRequest();
                })
                ->sortable()
                ->rules('required', 'alpha_num', 'max:20')
                ->creationRules('unique:clients,document_number')
                ->updateRules('unique:clients,document_number,{{resourceId}}'),

            Text::make('Razón social', 'legal_name')
                ->sortable()
                ->rules('required', 'max:100'),

            Text::make('Nombre comercial', 'business_name')
                ->sortable()
                ->rules('nullable', 'max:100'),

            BelongsTo::make('Ejecutivo de cuenta', 'user', NovaUser::class)
                ->sortable()
                ->rules('required')
                // ->searchable()
                ->withoutTrashed()
                ->relatableQueryUsing(function (NovaRequest $request, Builder $query) {
                    $query->whereIn('area_id', [AreasEnum::COMMERCIAL->id(), AreasEnum::BI->id()]);
                }),

        ];
    }

    /**
     * Get the cards available for the resource.
     *
     * @return array<int, \Laravel\Nova\Card>
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array<int, \Laravel\Nova\Filters\Filter>
     */
    public function filters(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array<int, \Laravel\Nova\Lenses\Lens>
     */
    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array<int, \Laravel\Nova\Actions\Action>
     */
    public function actions(NovaRequest $request): array
    {
        return [
            ExportAsCsv::make(),
        ];
    }

    /**
     * Return the location to redirect the user after creation.
     *
     * @return \Laravel\Nova\URL|string
     */
    public static function redirectAfterCreate(NovaRequest $request, $resource)
    {
        return '/resources/'.static::uriKey();
    }

    /**
     * Return the location to redirect the user after update.
     *
     * @return \Laravel\Nova\URL|string
     */
    public static function redirectAfterUpdate(NovaRequest $request, $resource)
    {
        return '/resources/'.static::uriKey();
    }
}
