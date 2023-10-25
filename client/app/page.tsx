'use client';

import * as sprintf from 'sprintf-js';
import {GoogleMap, LoadScript, Marker} from '@react-google-maps/api';
import {useEffect, useMemo, useRef, useState} from "react";

const initialMapCenter = {
    lat: 48.8066729,
    lng: 2.3067282
};

export default function Home() {
    const mapRef = useRef<google.maps.Map | null>(null);
    const [markersData, setMarkersData] = useState([]);
    const [mapCenter, setMapCenter] = useState(initialMapCenter);

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

    const handleMapLoad = (map: google.maps.Map | null) => {
        console.log('handleMapLoad')

        mapRef.current = map;
    };

    const handleMapDragEnd = () => {
        console.log('handleMapDragEnd')

        const map = mapRef.current;
        if (!map) return;

        const center = map.getCenter();
        if (!center) return;

        const newCenter: google.maps.LatLngLiteral = center.toJSON();
        const zoom: number | undefined = map.getZoom();

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

        let url: string = process.env.NEXT_PUBLIC_GAS_STATIONS_MAP as string;
        const formattedString = sprintf.sprintf(url+"?latitude=%s&longitude=%s&radius=%s", newCenter.lat, newCenter.lng, widthKm*1000);

        fetch(formattedString)
            .then((response) => response.json())
            .then((data) => {
                setMarkersData(data['hydra:member']);
        });
    };

    const containerStyle = {
        width: '100%',
        height: 'calc(100% - 4rem)',
    };

    useEffect(() => {
        let url: string = process.env.NEXT_PUBLIC_GAS_STATIONS_MAP as string;

        fetch(url)
            .then((response) => response.json())
            .then((data) => {
                setMarkersData(data['hydra:member']);
            });
    }, []);

    return (
      <LoadScript
          googleMapsApiKey={process.env.NEXT_PUBLIC_GOOGLE_API_KEY as string}>
          <GoogleMap
              mapContainerClassName="map-container"
              options={mapOptions}
              mapContainerStyle={containerStyle}
              zoom={12}
              center={mapCenter}
              onLoad={handleMapLoad}
              onDragEnd={handleMapDragEnd}
              onZoomChanged={handleMapDragEnd}
          >
              {Array.isArray(markersData) &&
                  markersData.map((marker, index) => (
                      <Marker
                          key={index}
                          position={{ lat: parseFloat(marker["address"]["latitude"]), lng: parseFloat(marker["address"]["longitude"]) }}
                      />
                  ))}
          </GoogleMap>
      </LoadScript>
  );
}
