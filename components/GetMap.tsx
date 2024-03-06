'use client';

import { MapContainer, TileLayer, Marker, Popup } from 'react-leaflet'
import 'leaflet/dist/leaflet.css'
import 'leaflet-defaulticon-compatibility/dist/leaflet-defaulticon-compatibility.css'
import "leaflet-defaulticon-compatibility";
import { useState } from 'react';

export default function GetMap() {

    const [markers, setMarkers] = useState([]);

    return (
        <MapContainer center={[48.761335765074705, 2.3663450252805784]}
            zoom={13}
            scrollWheelZoom={true}
            style={{ height: "calc(100% - 4rem)", width: "100%" }}
        >
            <TileLayer
                attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                url="http://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png"
            />
            <Marker position={[48.761335765074705, 2.3663450252805784]} draggable={false}>
                <Popup>
                    Hey ! you found me
                </Popup>
            </Marker>
        </MapContainer>
    );
}
