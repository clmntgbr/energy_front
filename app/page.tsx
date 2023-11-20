'use client';

import * as sprintf from 'sprintf-js';
import {GoogleMap, InfoWindow, Marker, useJsApiLoader} from '@react-google-maps/api';
import {SetStateAction, useEffect, useRef, useState} from "react";
import Loader from "@/components/Loader";

const initialMapCenter = {
    lat: 48.853,
    lng: 2.35,
};

export default function Home() {

    const {isLoaded} = useJsApiLoader({
        id: 'google-map-script',
        googleMapsApiKey: process.env.NEXT_PUBLIC_GOOGLE_API_KEY as string
    })

    let mapRef = useRef<google.maps.Map | null>(null);
    const [markersData, setMarkersData] = useState([]);
    const [mapCenter, setMapCenter] = useState(initialMapCenter);
    const [selectedMarker, setSelectedMarker] = useState(null);

    const handleMarkerClick = (marker: SetStateAction<null>) => {
        setSelectedMarker(marker);
    };

    const handleMapLoad = (map: google.maps.Map | null) => {
        console.log("handleMapLoad");

        navigator.geolocation.getCurrentPosition(
            function(position) {
                userFound(position, map);
            },
            function (positionError) {
                userNotFound(map);
            }
        );

        mapRef.current = map;
    };

    const userFound = (position: GeolocationPosition, map: google.maps.Map | null) => {
        const url: string = process.env.NEXT_PUBLIC_GAS_STATIONS_MAP as string;
        const center = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
        const formattedString = sprintf.sprintf(url + "?latitude=%s&longitude=%s&zoom=%s", center.lat, center.lng, map?.getZoom());
        fetchUrl(formattedString);
        map?.setCenter(center);
    }

    const userNotFound = (map: google.maps.Map | null) => {
        const url: string = process.env.NEXT_PUBLIC_GAS_STATIONS_MAP as string;
        const center = new google.maps.LatLng(initialMapCenter.lat, initialMapCenter.lng);
        const formattedString = sprintf.sprintf(url + "?latitude=%s&longitude=%s&zoom=%s", initialMapCenter.lat, initialMapCenter.lng, map?.getZoom());
        fetchUrl(formattedString);
        map?.setCenter(center);
    }

    const fetchUrl = (url: string) => {
        fetch(url)
            .then((response) => response.json())
            .then((data) => {
                setMarkersData(data['hydra:member']);
            });
    }

    const handleMapDragEnd = () => {
        console.log('handleMapDragEnd')

        const map = mapRef.current;
        if (!map) return;

        const center = map.getCenter();
        if (!center) return;

        const newCenter: google.maps.LatLngLiteral = center.toJSON();
        setMapCenter(newCenter);

        const url: string = process.env.NEXT_PUBLIC_GAS_STATIONS_MAP as string;
        const formattedString = sprintf.sprintf(url + "?latitude=%s&longitude=%s&zoom=%s", newCenter.lat, newCenter.lng, map.getZoom());
        fetchUrl(formattedString);
    };

    const popUp = (marker: never) => {
        return (
            <div className={'stations_map'}>
                <img src={process.env.NEXT_PUBLIC_GAS_BACK_URL + marker['imagePath']} alt="Marker Image" />
                <h3>{marker['name']}</h3>
                <p className={'address'}>{marker['address']['vicinity']}</p>
                <a className={'link'} href={'gas_station/' + marker['uuid']} target="_blank">Voir plus</a>
            </div>
        );
    }

    useEffect(() => {
    }, []);

    return (
        isLoaded ? (
            <GoogleMap
                mapContainerClassName="map-container"
                options={{
                    disableDefaultUI: false,
                    clickableIcons: false,
                    scrollwheel: true,
                    fullscreenControl: false,
                    keyboardShortcuts: false,
                    rotateControl: false,
                    streetViewControl: false,
                    mapTypeControl: true,
                    mapTypeControlOptions: {
                        mapTypeIds: ['roadmap'],
                    },
                }}
                mapContainerStyle={{
                    width: '100%',
                    height: 'calc(100% - 4rem)',
                }}
                zoom={13}
                center={mapCenter}
                onLoad={handleMapLoad}
                onDragEnd={handleMapDragEnd}
                onClick={() => setSelectedMarker(null)}
                onZoomChanged={handleMapDragEnd}
            >
                {
                    Array.isArray(markersData) && markersData.map((marker, index) => (
                        <Marker
                            key={marker["uuid"]}
                            onClick={() => handleMarkerClick(marker)}
                            position={{ lat: parseFloat(marker["address"]["latitude"]), lng: parseFloat(marker["address"]["longitude"]) }}
                        >
                        </Marker>
                    ))
                }


                {selectedMarker && (
                    <InfoWindow
                        position={{ lat: parseFloat(selectedMarker["address"]["latitude"]), lng: parseFloat(selectedMarker["address"]["longitude"]) }}
                        onCloseClick={() => setSelectedMarker(null)}
                    >
                        {popUp(selectedMarker)}
                    </InfoWindow>
                )}

            </GoogleMap>
        ) : (
            <Loader></Loader>
        )
  );
}
