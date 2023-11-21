// 'use client';
//
// import {RequestInfo} from 'undici-types';
// import useSWR from "swr";
//
// const fetcher = (url: RequestInfo) => fetch(url).then((res) => res.json());
//
//
// export default function GetGasStationByUuid({ params }: { params: { uuid: string } }) {
//   const { data, error, isLoading } = useSWR(
//       "https://back.traefik.me/api/gas_stations/"+ params.uuid,
//       fetcher
//   );
//   return <p>Gas Station by UUid { params.uuid }</p>;
// }
