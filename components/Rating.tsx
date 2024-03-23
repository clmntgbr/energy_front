import React from 'react';

class Rating extends React.Component<{ initialValue: any }> {
    render() {
        let {initialValue} = this.props;
        if (!initialValue) {
            return (<></>);
        }

        return (
            <div style={{paddingLeft: '10px', marginBottom: '3px'}}>
                {[1, 2, 3, 4, 5].map((value) => (
                    <span
                        key={value}
                        style={{cursor: 'pointer', color: value <= initialValue ? 'gold' : 'gray', fontSize: '20px', lineHeight: 0}}
                    >
                        ★
                    </span>
                ))}
            </div>
        );
    }
}

export default Rating;
