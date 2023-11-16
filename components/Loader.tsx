import React from "react";
import {GooeyCircleLoader} from "react-loaders-kit";

function Loader() {

    const loaderProps = {
        loading: true,
        size: 400,
        duration: 2,
        colors: ["#99fffe", "#f42e00", "#042549"],
    };

    return (
        <div className="loader">
            <GooeyCircleLoader {...loaderProps}/>
        </div>
    );
}

export default Loader;