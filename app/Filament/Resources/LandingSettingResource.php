<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LandingSettingResource\Pages;
use App\Models\LandingSetting;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LandingSettingResource extends Resource
{
    protected static ?string $model = LandingSetting::class;
    protected static ?string $navigationIcon = 'heroicon-s-cog-6-tooth';
    protected static ?string $navigationGroup = 'Configuración Web';
    protected static ?string $modelLabel = 'Ajuste de la Página Web';
    protected static ?string $pluralModelLabel = 'Ajustes de la Página Web';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            // Grid principal de 3 columnas para separar contenido de la navegación
            Grid::make(3)
                ->schema([                    
                    // Formularios dinámicos
                    Group::make()
                        ->schema([
                            Section::make('Información Corporativa')
                                ->description('Dirección, correo de contacto y mapa para el Footer y la sección de contacto.')
                                ->icon('heroicon-o-building-office')
                                ->visible(fn (Forms\Get $get) => $get('key') === 'landing_business_info')
                                ->schema([
                                    TextInput::make('info_address')->label('Dirección Física')->required(),
                                    TextInput::make('info_email')->label('Correo Electrónico')->email()->required(),
                                    Textarea::make('info_maps')
                                        ->label('Iframe de Google Maps (Atributo src)')
                                        ->placeholder('https://www.google.com/maps/embed?...')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ])->columns(2),

                            // Formulario Simple (Áreas y Horarios)
                            KeyValue::make('value_simple_json')
                                ->label('Listado de Datos')
                                ->keyLabel('Identificador o Día')
                                ->valueLabel('Descripción o Horario')
                                ->visible(fn (Forms\Get $get) => in_array($get('key'), ['landing_reservation_areas', 'landing_opening_hours'])),

                            // Hero Principal
                            Section::make('Diseño del Banner Principal')
                                ->description('Administra la primera impresión visual de la página web.')
                                ->icon('heroicon-o-sparkles')
                                ->visible(fn (Forms\Get $get) => $get('key') === 'landing_hero_section')
                                ->schema([
                                    TextInput::make('hero_title')->label('Título Principal')->required(),
                                    TextInput::make('hero_subtitle')->label('Subtítulo o Slogan')->required(),
                                    TextInput::make('hero_video_url')->label('Enlace de Video Promocional (YouTube)')->url()->columnSpanFull(),
                                    FileUpload::make('hero_main_image')->label('Imagen de Fondo de Reserva')->image()->directory('landing')->columnSpanFull(),
                                ])->columns(2),

                            // Redes Sociales
                            Section::make('Enlaces de Contacto')
                                ->description('Controla las redirecciones a tus perfiles oficiales.')
                                ->icon('heroicon-o-share')
                                ->visible(fn (Forms\Get $get) => $get('key') === 'landing_social_links')
                                ->schema([
                                    TextInput::make('social_facebook')->label('Facebook URL')->url(),
                                    TextInput::make('social_instagram')->label('Instagram URL')->url(),
                                    TextInput::make('social_whatsapp')->label('Celular de WhatsApp (Ej: 57300...)'),
                                ])->columns(3),

                            // Menú Gastronómico
                            Section::make('Menú de la Casa')
                                ->description('Agrega, edita o elimina los productos estrella de la carta.')
                                ->icon('heroicon-o-cake')
                                ->visible(fn (Forms\Get $get) => $get('key') === 'landing_featured_menu')
                                ->schema([
                                    Repeater::make('value_menu')
                                        ->label('Platos o Bebidas')
                                        ->schema([
                                            TextInput::make('name')->label('Nombre')->required(),
                                            TextInput::make('price')->label('Precio ($)')->numeric()->required(),
                                            TextInput::make('description')->label('Descripción / Ingredientes')->columnSpanFull()->required(),
                                            FileUpload::make('image')->label('Fotografía')->image()->directory('landing/menu')->columnSpanFull(),
                                        ])->columns(2)->itemLabel(fn (array $state): ?string => $state['name'] ?? null)->collapsible()
                                ]),

                            // Eventos
                            Section::make('Cartelera de Espectáculos')
                                ->description('Gestiona los eventos en vivo y fiestas del fin de semana.')
                                ->icon('heroicon-o-musical-note')
                                ->visible(fn (Forms\Get $get) => $get('key') === 'landing_events_gallery')
                                ->schema([
                                    Repeater::make('value_events')
                                        ->label('Programación de Shows')
                                        ->schema([
                                            TextInput::make('title')->label('Título del Show')->required(),
                                            TextInput::make('date')->label('Fecha / Cuándo es')->required(),
                                            TextInput::make('description')->label('Artistas / Descripción')->columnSpanFull()->required(),
                                            FileUpload::make('image')->label('Flyer Publicitario')->image()->directory('landing/events')->columnSpanFull(),
                                        ])->columns(2)->itemLabel(fn (array $state): ?string => $state['title'] ?? null)->collapsible()
                                ]),

                            // Galería Flotante (Múltiples Imágenes)
                            Section::make('Álbum de Fotos del Establecimiento')
                                ->description('Arrastra todas las fotos que quieras mostrar en el carrusel o galería de ambiente.')
                                ->icon('heroicon-o-camera')
                                ->visible(fn (Forms\Get $get) => $get('key') === 'landing_gallery')
                                ->schema([
                                    FileUpload::make('value_gallery')
                                        ->label('Imágenes del Establecimiento')
                                        ->image()
                                        ->multiple()
                                        ->reorderable()
                                        ->appendFiles()
                                        ->directory('landing/galeria')
                                        ->columnSpanFull(),
                                ]),

                            // SEO Tags
                            Section::make('Optimización SEO para Motores de Búsqueda')
                                ->description('Controla cómo se muestra El Palomo Negro al compartir el link o buscar en Google.')
                                ->icon('heroicon-o-magnifying-glass')
                                ->visible(fn (Forms\Get $get) => $get('key') === 'landing_seo_tags')
                                ->schema([
                                    TextInput::make('seo_title')->label('Meta Título de la Página')->required()->columnSpanFull(),
                                    Textarea::make('seo_description')->label('Meta Descripción (Resumen en Google)')->rows(3)->required()->columnSpanFull(),
                                    TextInput::make('seo_keywords')->label('Palabras Clave (Separadas por comas)')->placeholder('bar, restaurante, campestre')->columnSpanFull(),
                                ]),

                            // Píxeles de Seguimiento
                            Section::make('Métricas y Píxeles de Marketing')
                                ->description('Inserta los IDs de tus herramientas de medición de anuncios y analítica.')
                                ->icon('heroicon-o-chart-bar')
                                ->visible(fn (Forms\Get $get) => $get('key') === 'landing_pixel_ids')
                                ->schema([
                                    TextInput::make('pixel_google')->label('Google Analytics ID (G-XXXXXX)'),
                                    TextInput::make('pixel_facebook')->label('Facebook Pixel ID'),
                                    TextInput::make('pixel_tiktok')->label('TikTok Pixel ID'),
                                ])->columns(1),
                        ])
                        ->columnSpan(2),

                    // 🧭 COLUMNA DERECHA (Ocupa 1 columna): Panel de Control Flotante/Lateral
                    Group::make()
                        ->schema([
                            Section::make('Navegación Rápida')
                                ->description('Intercambia entre módulos sin salir de la edición.')
                                ->schema([
                                    Select::make('id')
                                        ->label('Sección Activa')
                                        ->options(function () {
                                            return \App\Models\LandingSetting::all()->pluck('label', 'id')->toArray();
                                        })
                                        ->selectablePlaceholder(false)
                                        ->live()
                                        ->afterStateUpdated(function ($state) {
                                            if (!$state) return;
                                            redirect()->to(static::getUrl('edit', ['record' => $state]));
                                        }),
                                ]),
                        ])
                        ->columnSpan(1),
                ]),

            Hidden::make('key'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->contentGrid([
            'default' => 1,
            'sm' => 2,
            'md' => 3,
            'xl' => 4,
        ])
        ->columns([
            Tables\Columns\Layout\Stack::make([
                // Ícono estético según el tipo de sección
                Tables\Columns\TextColumn::make('icon')
                    ->default(fn ($record) => match($record->key) {
                        'landing_hero_section' => '✨',
                        'landing_featured_menu' => '🍽️',
                        'landing_events_gallery' => '🎉',
                        'landing_opening_hours' => '🕒',
                        'landing_reservation_areas' => '🪑',
                        'landing_gallery' => '📸',
                        default => '📱',
                    })
                    ->size(Tables\Columns\TextColumn\TextColumnSize::Large)
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('label')
                    ->weight('bold')
                    ->size(Tables\Columns\TextColumn\TextColumnSize::Medium)
                    ->alignCenter()
                    ->description(fn ($record) => match($record->key) {
                        'landing_hero_section' => 'Título principal, imágenes y video de fondo.',
                        'landing_featured_menu' => 'Platos fuertes, cócteles y precios de la casa.',
                        'landing_events_gallery' => 'Próximos shows, conciertos y fechas especiales.',
                        'landing_opening_hours' => 'Días y rangos de atención al público.',
                        'landing_reservation_areas' => 'Configuración de palcos, VIP y general.',
                        'landing_gallery' => 'Galería de imágenes.',
                        default => 'Enlaces a Facebook, Instagram y WhatsApp.',
                    }, position: 'below'),
            ])->space(2)
        ])
        ->actions([            
            Tables\Actions\EditAction::make()
                ->label('Gestionar Sección')
                ->button()
                ->color('amber')
                ->icon('heroicon-m-pencil-square'),
        ])
        ->actionsColumnLabel('Acciones');
    }

    public static function canCreate(): bool
    {
        return false; // No se crean nuevas llaves desde el panel
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLandingSettings::route('/'),
            'edit' => Pages\EditLandingSetting::route('/{record}/edit'),
        ];
    }
}