'use client';

import { MapContainer, Marker, Popup, TileLayer, useMapEvents } from 'react-leaflet'
import 'leaflet/dist/leaflet.css'
import 'leaflet-defaulticon-compatibility/dist/leaflet-defaulticon-compatibility.css'
import "leaflet-defaulticon-compatibility";
import { useEffect, useRef, useState } from 'react';
import * as sprintf from 'sprintf-js';
import L, { DragEndEvent, LatLng, Point } from 'leaflet';
import energyStationPopUp from './EnergyStationPopUp';

const initialMapCenter = {
    lat: 48.853,
    lng: 2.35,
};

export default function GetMap() {

    const [markers, setMarkers] = useState([]);
    const [gasTypes, setGasTypes] = useState([]);
    const [mapCenter, setMapCenter] = useState(initialMapCenter);
    const [radius, setRadius] = useState(10247);
    const [userPosition, setUserPosition] = useState<LatLng | null>(null);


    const [selectedZoom, setSelectedZoom] = useState(11);
    const [selectedMarker, setSelectedMarker] = useState(null);
    const [selectedEnergyType, setSelectedEnergyType] = useState(process.env.NEXT_PUBLIC_GAS_TYPE_UUID as string);
    const [selectedEnergyStationType, setSelectedEnergyStationType] = useState('GAS');

    const leafletMap = useRef(null);

    useEffect(() => {
    }, []);

    const fetchEnergyStationsUrl = (url: string) => {
        console.log('fetchEnergyStationsUrl')
        fetch(url)
            .then((response) => response.json())
            .then((data) => {
                setSelectedMarker(null);
                setMarkers(data['hydra:member']);
            });
    }

    const fetchGasTypesUrl = (url: string) => {
        console.log('fetchGasTypesUrl')
        fetch(url)
            .then((response) => response.json())
            .then((data) => {
                setGasTypes(data['hydra:member']);
            });
    }

    function HandleComponent() {

        const map = useMapEvents({
            locationfound(event) {
                setUserPosition(event.latlng);
                map.flyTo(event.latlng, 13);
                setSelectedZoom(13);
                userFound(map, event.latlng.lat, event.latlng.lng);
            },
            dragend(event: DragEndEvent) {
                const LatLng = event.target.getCenter();
                const zoom = event.target.getZoom();
                dragEndEnergyStations(map, LatLng.lat, LatLng.lng, zoom);
            },
            zoomend(event) {
                const LatLng = event.target.getCenter();
                const zoom = event.target.getZoom();
                dragEndEnergyStations(map, LatLng.lat, LatLng.lng, zoom);
            },
        });

        return userPosition === null ? null : (
            <Marker
                position={userPosition}
                icon={L.icon({
                    iconUrl: 'find_way_icon.png',
                    iconSize: [50, 50]
                })}
            >
            </Marker>
        );
    }

    const dragEndEnergyStations = (map: any, latitude: number, longitude: number, zoom: number) => {

        setMapCenter({ lat: latitude, lng: longitude });
        setSelectedZoom(zoom);

        const mapRadius = getRadius(map);
        const url: string = process.env.NEXT_PUBLIC_GAS_STATIONS_MAP as string;
        const formattedString = sprintf.sprintf(url + "?latitude=%s&longitude=%s&radius=%s&energy_type_uuid=%s&energy_station_type=%s", latitude, longitude, mapRadius, selectedEnergyType, selectedEnergyStationType);

        fetchEnergyStationsUrl(formattedString);
    }

    const userFound = (map: any, latitude: number, longitude: number) => {

        setMapCenter({ lat: latitude, lng: longitude });

        const mapRadius = getRadius(map);
        const url: string = process.env.NEXT_PUBLIC_GAS_STATIONS_MAP as string;
        const formattedString = sprintf.sprintf(url + "?latitude=%s&longitude=%s&radius=%s&energy_type_uuid=%s&energy_station_type=%s", latitude, longitude, mapRadius, selectedEnergyType, selectedEnergyStationType);

        fetchEnergyStationsUrl(formattedString);
    }

    const userNotFound = (map: any) => {

        const mapRadius = getRadius(map);
        const url: string = process.env.NEXT_PUBLIC_GAS_STATIONS_MAP as string;
        const formattedString = sprintf.sprintf(url + "?latitude=%s&longitude=%s&radius=%s&energy_type_uuid=%s&energy_station_type=%s", initialMapCenter.lat, initialMapCenter.lng, mapRadius, selectedEnergyType, selectedEnergyStationType);

        fetchEnergyStationsUrl(formattedString);
    }

    const getRadius = (map: { getBounds: () => any; }) => {

        let mapRadius = radius;
        const bounds = map.getBounds();

        if (bounds) {
            const ne = bounds.getNorthEast();
            const sw = bounds.getSouthWest();

            if (ne && sw) {
                const latDiff = Math.abs(ne.lat - sw.lat);
                const metersPerDegree = 111000;
                mapRadius = latDiff * metersPerDegree;
                setRadius(mapRadius);
            }
        }

        return mapRadius;
    }

    const handleMapLoad = () => {
        let url: string = process.env.NEXT_PUBLIC_GAS_TYPES as string;
        fetchGasTypesUrl(url);

        navigator.geolocation.getCurrentPosition(
            (position) => {
                if (!leafletMap) return;
                if (!leafletMap.current) return;
                const current: any = leafletMap.current;
                current.locate();
            },
            (error) => {
                if (!leafletMap) return;
                if (!leafletMap.current) return;
                userNotFound(leafletMap.current);
            }
        );
    };

    return (
        <MapContainer
            ref={leafletMap}
            center={mapCenter}
            zoom={selectedZoom}
            scrollWheelZoom={true}
            whenReady={handleMapLoad}
            style={{ height: "calc(100% - 4rem)", width: "100%" }}
        >
            <TileLayer
                attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                url="https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png"
            />
            <HandleComponent />
            {
                Array.isArray(markers) && markers.map((marker, index) => (
                    <Marker
                        key={marker["uuid"]}
                        eventHandlers={{
                            click: () => {
                                if (!leafletMap) return;
                                if (!leafletMap.current) return;

                                console.log(leafletMap);
                                leafletMap.current.flyTo({ lat: parseFloat(marker["address"]["latitude"]), lng: parseFloat(marker["address"]["longitude"]) }, 13);
                            },
                        }}
                        position={{ lat: parseFloat(marker["address"]["latitude"]), lng: parseFloat(marker["address"]["longitude"]) }}
                    >
                        <Popup
                            // autoPan={true}
                            // keepInView={false}
                            // autoPanPaddingBottomRight={new Point(10, 10)}
                            // autoPanPaddingTopLeft={new Point(10, 10)}
                            // autoPanPadding={new Point(10, 10)}
                        >
                            {energyStationPopUp(marker)}
                        </Popup>
                    </Marker>
                ))
            }
        </MapContainer>
    );
}

