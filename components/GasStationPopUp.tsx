import React from "react";
import * as sprintf from "sprintf-js";
import { TbMapShare } from "react-icons/tb";
import Rating from "@/components/Rating";

function EnergyStationPopUp(marker: never) {
    const url = sprintf.sprintf('https://google.com/maps/search/?query=%s,%s&api=1', marker['address']['latitude'], marker['address']['longitude']);
    return (
        <div className={'stations_map'}>
            <a className={'google_map_link'} href={url} target="_blank"><TbMapShare></TbMapShare></a>
            <img src={process.env.NEXT_PUBLIC_GAS_BACK_URL + marker['imagePath']} alt="Marker Image" />
            <h3>{marker['name']}</h3>
            <Rating initialValue={marker['googlePlace']['rating']}></Rating>
            <a className={'address_street'} onClick={() => handleCopy(marker)}>{marker['address']['number']} {marker['address']['street']}</a>
            <a className={'address_city'} onClick={() => handleCopy(marker)}>{marker['address']['postalCode']}, {marker['address']['city']}</a>
            <div className="container_prices">
                {getLastPrices(marker)}
            </div>
            <div className="container_services">
                {getServices(marker)}
            </div>
            <a className={'link'} href={'energy_station/' + marker['uuid']} target="_blank">Accèder à la station</a>
        </div>
    );
}

const getLastPrices = (marker: never) => {
    const lastPrices = marker["lastPrices"];
    let elements: React.JSX.Element[] = [];

    lastPrices.map((price: { [x: string]: any; }, index: any) => {
        elements.push((
            <div className="box_prices" key={price['energyPriceId']}>
                <p className={`box_price_name`}>{price['energyTypeLabel']}</p>
                <p className={`box_price ${price['energyPriceDifference']}`}>{price['energyPriceValue'] / 1000}€</p>
            </div>
        ));
    });

    return elements;
};

const getServices = (marker: never) => {
    const energyServices = marker["energyServices"];
    let elements: React.JSX.Element[] = [];

    energyServices.map((energyService: { [x: string]: any; }, index: any) => {
        const isLastElement = index === energyServices.length - 1;
        const comma = isLastElement ? '' : ', ';

        elements.push((
            <span className={`energy_service`} key={energyService['uuid']}>{energyService['name']}{comma} </span>
        ));
    });

    return elements;
};

const handleCopy = (marker: never) => {
    const address = sprintf.sprintf('%s %s, %s %s', marker['address']['number'], marker['address']['street'], marker['address']['postalCode'], marker['address']['city'])
    navigator.clipboard.writeText(address);
};

export default EnergyStationPopUp;
