import React from "react";
import * as sprintf from "sprintf-js";
import {TbMapShare} from "react-icons/tb";
import Rating from "@/components/Rating";

function GasStationPopUp(marker: never) {
    console.log(marker['googlePlace']['rating'])
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
                {generateElements(marker)}
            </div>
            <a className={'link'} href={'gas_station/' + marker['uuid']} target="_blank">Accèder à la station</a>
        </div>
    );
}

const generateElements = (marker: never) => {
    const prices = marker["lastPrices"];
    let elements: React.JSX.Element[] = [];

    prices.map((price: { [x: string]: any; }, index: any) => {
        elements.push((
            <div className="box_prices" key={price['gasPriceId']}>
                <p className={`box_price_name`}>{price['gasTypeLabel']}</p>
                <p className={`box_price ${price['gasPriceDifference']}`}>{price['gasPriceValue']/1000}€</p>
            </div>
        ));
    });

    return elements;
};

const handleCopy = (marker: never) => {
    const address = sprintf.sprintf('%s %s, %s %s', marker['address']['number'], marker['address']['street'], marker['address']['postalCode'], marker['address']['city'])
    navigator.clipboard.writeText(address);
};

export default GasStationPopUp;