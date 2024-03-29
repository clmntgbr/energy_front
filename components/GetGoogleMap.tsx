'use client';

import * as sprintf from 'sprintf-js';
import { GoogleMap, InfoWindow, Marker, useJsApiLoader } from '@react-google-maps/api';
import { SetStateAction, useEffect, useMemo, useRef, useState } from "react";
import Loader from "@/components/Loader";
import { Button, Dropdown, DropdownItem, DropdownMenu, DropdownTrigger } from "@nextui-org/react";
import { Key } from "swr";
import EnergyStationPopUp from "@/components/EnergyStationPopUp";

const initialMapCenter = {
    lat: 48.853,
    lng: 2.35,
};

export default function GetGoogleMap() {

    const { isLoaded } = useJsApiLoader({
        id: 'google-map-script',
        googleMapsApiKey: process.env.NEXT_PUBLIC_GOOGLE_API_KEY as string
    })

    let mapRef = useRef<google.maps.Map | null>(null);
    const [markersData, setMarkersData] = useState([]);
    const [energyTypesData, setEnergyTypesData] = useState([]);
    const [mapCenter, setMapCenter] = useState(initialMapCenter);
    const [selectedMarker, setSelectedMarker] = useState(null);
    const [selectedEnergyType, setSelectedEnergyType] = useState(new Set(["E10"]));
    const [energyTypeUuid, setEnergyTypeUuid] = useState(process.env.NEXT_PUBLIC_GAS_TYPE_UUID as string);
    const [radius, setRadius] = useState(10247);

    const selectedValue = useMemo(
        () => Array.from(selectedEnergyType).join(", ").replaceAll("_", " "),
        [selectedEnergyType]
    );

    const onEnergyTypesChange = (key: Key) => {
        let uuid = energyTypeUuid;
        energyTypesData.map((type, index) => {
            if (type['name'] === key) {
                uuid = type['uuid'];
                setEnergyTypeUuid(type['uuid']);
            }
        });

        const url: string = process.env.NEXT_PUBLIC_GAS_STATIONS_MAP as string;
        const formattedString = sprintf.sprintf(url + "?latitude=%s&longitude=%s&radius=%s&energy_type_uuid=%s", mapCenter.lat, mapCenter.lng, radius, uuid);
        fetchEnergyStationsUrl(formattedString);
    };

    const onEnergyTypeSelected = (key: Key) => {
        // @ts-ignore
        setSelectedEnergyType(key);
    };

    const handleMarkerClick = (marker: SetStateAction<null>) => {
        setSelectedMarker(marker);
    };

    const handleMapLoad = (map: google.maps.Map | null) => {
        console.log("handleMapLoad");

        if (!map) return;

        const url: string = process.env.NEXT_PUBLIC_GAS_TYPES as string;
        fetchEnergyTypesUrl(url);

        google.maps.event.addListenerOnce(map, 'tilesloaded', function () {
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    userFound(position, map);
                },
                function (positionError) {
                    userNotFound(map);
                }
            );

            mapRef.current = map;
        });
    };

    const userFound = (position: GeolocationPosition, map: google.maps.Map) => {
        const newRadius = getRadius(map);
        const url: string = process.env.NEXT_PUBLIC_GAS_STATIONS_MAP as string;
        const center = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
        const formattedString = sprintf.sprintf(url + "?latitude=%s&longitude=%s&radius=%s&energy_type_uuid=%s", center.lat, center.lng, newRadius, energyTypeUuid);
        fetchEnergyStationsUrl(formattedString);
        map?.setCenter(center);
        setMapCenter({ lat: position.coords.latitude, lng: position.coords.longitude });
    }

    const userNotFound = (map: google.maps.Map) => {
        const newRadius = getRadius(map);
        const url: string = process.env.NEXT_PUBLIC_GAS_STATIONS_MAP as string;
        const center = new google.maps.LatLng(initialMapCenter.lat, initialMapCenter.lng);
        const formattedString = sprintf.sprintf(url + "?latitude=%s&longitude=%s&radius=%s&energy_type_uuid=%s", initialMapCenter.lat, initialMapCenter.lng, newRadius, energyTypeUuid);
        fetchEnergyStationsUrl(formattedString);
        map?.setCenter(center);
    }

    const fetchEnergyStationsUrl = (url: string) => {
        fetch(url)
            .then((response) => response.json())
            .then((data) => {
                setSelectedMarker(null);
                setMarkersData(data['hydra:member']);
            });
    }

    const fetchEnergyTypesUrl = (url: string) => {
        fetch(url)
            .then((response) => response.json())
            .then((data) => {
                setEnergyTypesData(data['hydra:member']);
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

        const newRadius = getRadius(map);

        const url: string = process.env.NEXT_PUBLIC_GAS_STATIONS_MAP as string;
        const formattedString = sprintf.sprintf(url + "?latitude=%s&longitude=%s&radius=%s&energy_type_uuid=%s", newCenter.lat, newCenter.lng, newRadius, energyTypeUuid);
        fetchEnergyStationsUrl(formattedString);
    };


    const getRadius = (map: google.maps.Map) => {

        let newRadius = radius;

        const bounds = map.getBounds();

        if (bounds) {
            const ne = bounds.getNorthEast();
            const sw = bounds.getSouthWest();

            if (ne && sw) {
                const latDiff = Math.abs(ne.lat() - sw.lat());
                const metersPerDegree = 111000;
                newRadius = latDiff * metersPerDegree;
                setRadius(newRadius);
            }
        }


        return newRadius;
    }

    useEffect(() => {
    }, []);

    return (
        isLoaded ? (
            <>
                <Dropdown>
                    <DropdownTrigger>
                        <Button
                            variant="bordered"
                            className="capitalize"
                        >
                            {selectedValue}
                        </Button>
                    </DropdownTrigger>
                    <DropdownMenu
                        onAction={(key) => onEnergyTypesChange(key as string)}
                        variant="flat"
                        disallowEmptySelection
                        selectionMode="single"
                        selectedKeys={selectedEnergyType}
                        onSelectionChange={(key) => onEnergyTypeSelected(key as string)}
                    >
                        {
                            energyTypesData.map((type, index) => (
                                <DropdownItem
                                    key={type['name']}
                                    className={`energy_types energy_type_${type['reference']}`}
                                >{type['name']}
                                </DropdownItem>
                            ))
                        }
                    </DropdownMenu>
                </Dropdown>

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
                            {EnergyStationPopUp(selectedMarker)}
                        </InfoWindow>
                    )}

                </GoogleMap>
            </>
        ) : (
            <Loader></Loader>
        )
    );
}
