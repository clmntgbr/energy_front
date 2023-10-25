'use client'

import {GoogleMap} from "@react-google-maps/api";
import {useMemo} from "react";

interface GetGoogleMapProps {
    mapCenter: { lat: number, lng: number };
}

export default function GetGoogleMap({ mapCenter }: GetGoogleMapProps) {

    const mapOptions = useMemo<google.maps.MapOptions>(
        () => ({
            disableDefaultUI: false,
            clickableIcons: false,
            scrollwheel: true,
            fullscreenControl: false,
            keyboardShortcuts: false,
            rotateControl: false,
            streetViewControl: false,
            mapTypeControl: true, // Active le contrôle du type de carte
            mapTypeControlOptions: {
                mapTypeIds: ['roadmap'], // Limite le type de carte au plan
                style: google.maps.MapTypeControlStyle.DEFAULT, // Style par défaut du contrôle du type de carte
            },
        }),
        []
    );

    const markerOptions = {
        icon: {
            url: 'https://cdn-icons-png.flaticon.com/512/4284/4284088.png', // Remplacez par l'URL de votre icône personnalisée
            scaledSize: new window.google.maps.Size(300, 300), // Réglez la taille de l'icône
        },
    };

    return (
        <GoogleMap
            options={mapOptions}
            zoom={14}
            center={mapCenter}
            mapContainerStyle={{ width: '100%', height: 'calc(100% - 4rem)' }}
            onLoad={() => console.log('Map Component Loaded...')}>
        </GoogleMap>
    );
}