'use client';

import { RequestInfo } from 'undici-types';
import useSWR from "swr";

const fetcher = (url: RequestInfo) => fetch(url).then((res) => res.json());


export default function GetEnergyStationByUuid({ params }: { params: { uuid: string } }) {
  const { data, error, isLoading } = useSWR(
    "https://back.traefik.me/api/energy_stations/" + params.uuid,
    fetcher
  );
  return <p>Energy Station by UUid {params.uuid}</p>;
}
