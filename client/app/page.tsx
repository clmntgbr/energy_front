'use client';

import {useLoadScript} from '@react-google-maps/api';
import GetGoogleMap from "@/components/GoogleMap";

export default function Home() {

  const { isLoaded } = useLoadScript({
    googleMapsApiKey: process.env.NEXT_PUBLIC_GOOGLE_API_KEY as string
  });

  if (!isLoaded) {
    return <></>;
  }

  return (
    <GetGoogleMap
        latitude={48.8066729}
        longitude={2.3067282}
    />
  );
}
