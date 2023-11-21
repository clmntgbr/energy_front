'use client';

import * as sprintf from 'sprintf-js';
import {GoogleMap, InfoWindow, Marker, useJsApiLoader} from '@react-google-maps/api';
import {SetStateAction, useEffect, useMemo, useRef, useState} from "react";
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

    const mapRef = useRef<google.maps.Map | null>(null);
    const [markersData, setMarkersData] = useState([]);
    const [userLocation, setUserLocation] = useState({lat: initialMapCenter.lat, lng: initialMapCenter.lng});
    const [hasUserLocation, setHasUserLocation] = useState(false);
    const [mapCenter, setMapCenter] = useState(initialMapCenter);
    const [activeMarker, setActiveMarker] = useState(null);

    const mapOptions = useMemo<google.maps.MapOptions>(
        () => ({
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
        }),
        []
    );

    const handleActiveMarker = (marker: SetStateAction<null>) => {
        setActiveMarker(null)
        if (marker === activeMarker) {
            return;
        }
        setActiveMarker(marker);
    };

    const handleMapLoad = (map: google.maps.Map | null) => {
        let url: string = process.env.NEXT_PUBLIC_GAS_STATIONS_MAP as string;

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const newCenter = new google.maps.LatLng(position.coords.latitude, position.coords.longitude)
                setHasUserLocation(true);
                setUserLocation({lat: newCenter.lat(), lng: newCenter.lng()});
                map?.setCenter(newCenter)
                const formattedString = sprintf.sprintf(url + "?latitude=%s&longitude=%s&zoom=%s", newCenter.lat, newCenter.lng, map?.getZoom());
                fetchUrl(formattedString);
            },
            function (positionError) {
                const formattedString = sprintf.sprintf(url + "?latitude=%s&longitude=%s&zoom=%s", initialMapCenter.lat, initialMapCenter.lng, map?.getZoom());
                fetchUrl(formattedString);
            }
        );

        mapRef.current = map;
    };

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

        const bounds = map.getBounds();
        let widthKm: number = 20;
        if (bounds) {
            const northEast: google.maps.LatLng = bounds.getNorthEast();
            const southWest: google.maps.LatLng = bounds.getSouthWest();
            const earthRadiusKm = 6371;
            const latitude = southWest.lat() * (Math.PI / 180) - northEast.lat() * (Math.PI / 180);
            const longitude = southWest.lng() * (Math.PI / 180) - northEast.lng() * (Math.PI / 180);
            const a = Math.sin(latitude / 2) * Math.sin(latitude / 2) + Math.cos(northEast.lat() * (Math.PI / 180)) * Math.cos(southWest.lat() * (Math.PI / 180)) * Math.sin(longitude / 2) * Math.sin(longitude / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            widthKm = earthRadiusKm * c;
        }

        console.log(widthKm);

        let url: string = process.env.NEXT_PUBLIC_GAS_STATIONS_MAP as string;
        const formattedString = sprintf.sprintf(url + "?latitude=%s&longitude=%s&zoom=%s", newCenter.lat, newCenter.lng, map.getZoom());
        fetchUrl(formattedString);
    };

    const containerStyle = {
        width: '100%',
        height: 'calc(100% - 4rem)',
    };

    useEffect(() => {
    }, []);

    return (
        isLoaded ? (
            <GoogleMap
                mapContainerClassName="map-container"
                options={mapOptions}
                mapContainerStyle={containerStyle}
                zoom={13}
                center={mapCenter}
                onLoad={handleMapLoad}
                onDragEnd={handleMapDragEnd}
                onZoomChanged={handleMapDragEnd}
                onClick={() => setActiveMarker(null)}
            >
                {
                    Array.isArray(markersData) && markersData.map((marker, index) => (
                        <Marker
                            icon={{
                                url: marker['hasLowPrices'] ? process.env.NEXT_PUBLIC_GAS_BACK_URL + marker["gasStationBrand"]["imageLowPath"] : process.env.NEXT_PUBLIC_GAS_BACK_URL + marker["gasStationBrand"]["imagePath"],
                                scaledSize: new google.maps.Size(81, 125)
                            }}
                            onClick={() => handleActiveMarker(marker['uuid'])}
                            zIndex={marker['hasLowPrices'] ? 1000 : 1}
                            key={index}
                            position={{ lat: parseFloat(marker["address"]["latitude"]), lng: parseFloat(marker["address"]["longitude"]) }}
                        >
                            {activeMarker === marker['uuid'] ? (
                                <InfoWindow
                                    onCloseClick={() => setActiveMarker(null)}
                                >
                                    <div>hello</div>
                                </InfoWindow>
                            ) : null}
                        </Marker>
                    ))
                }



                {
                    hasUserLocation ?
                        <Marker
                            icon={{
                                url: '1d49088c27e64658b8bc35cb4812af4d.gif',
                                scaledSize: new google.maps.Size(100, 100)
                            }}
                            zIndex={0}
                            key={'user'}
                            position={{ lat: userLocation.lat, lng: userLocation.lng }}
                        /> : <></>
                }

            </GoogleMap>
        ) : (
            <Loader></Loader>
        )
  );
}
