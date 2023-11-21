'use client';

import {RequestInfo} from 'undici-types';
import useSWR from "swr";

const fetcher = (url: RequestInfo) => fetch(url).then((res) => res.json());


export default function GetGasStations() {
  const { data, error, isLoading } = useSWR(
      "https://back.traefik.me/api/gas_stations",
      fetcher
  );
  return <p>Gas Stations</p>;
}
